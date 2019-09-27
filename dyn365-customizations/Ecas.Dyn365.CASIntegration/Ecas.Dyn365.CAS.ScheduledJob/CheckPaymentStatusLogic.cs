using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.ServiceModel;
using Microsoft.Xrm.Sdk;
using Microsoft.Xrm.Sdk.Query;
using Microsoft.Crm.Sdk.Messages;
using System.Net;
using System.Net.Http;
using System.Net.Http.Headers;
using VSD.Common.XrmHelpers;

namespace Ecas.Dyn365.CAS.ScheduledJob.ScheduleJobSession
{
    public class CheckPaymentStatusLogic
    {
        #region Private Members
        private IOrganizationService OrgService { get; set; }

        private Entity Target { get; set; }

        private StringBuilder Log { get; set; }
        #endregion

        #region Constructor
        public CheckPaymentStatusLogic(IOrganizationService _service, Entity targetEntity, Entity jobEntity)
        {
            OrgService = _service;
            Target = targetEntity;
            Log = new StringBuilder();
            
            SetStateRequest req = new SetStateRequest();
            req.EntityMoniker = Target.ToEntityReference();
            req.State = new OptionSetValue(0);
            req.Status = new OptionSetValue(100000000); //In Progress

            _service.Execute(req);
        }
        #endregion

        #region Private Methods
        private List<Entity> GetPaymentsSentToCAS()
        {
            List<Entity> result = new List<Entity>();

            QueryExpression exp = new QueryExpression("");
            exp.NoLock = true;
            exp.ColumnSet.AddColumns("");
            exp.Criteria.AddCondition("modifiedon", ConditionOperator.OlderThanXDays, 1);
            exp.Criteria.AddCondition("statecode", ConditionOperator.Equal, 0); //Active
            exp.Criteria.AddCondition("statuscode", ConditionOperator.Equal, 100000001); //SentToCAS

            var coll = OrgService.RetrieveMultiple(exp);
            if (coll != null && coll.Entities != null && coll.Entities.Count > 0)
                result = coll.Entities.ToList();

            return result;
        }
        
        #endregion

        #region Public Methods
        public bool Execute()
        {
            bool isError = false;

            try
            {
                Log.AppendLine("\r\nOUTPUT PARAMETERS:");
                var configs = XrmHelpers.Helpers.GetSystemConfigurations(OrgService, "CAS", string.Empty, null);

                foreach (var paymentEntity in GetPaymentsSentToCAS())
                {
                    string userMessage = string.Empty;
                    HttpClient httpClient = null;
                    try
                    {
                        string clientKey = Helpers.GetConfigKeyValue(configs, "ClientKey", "CAS", null);
                        string clientId = Helpers.GetConfigKeyValue(configs, "ClientId", "CAS", null);
                        string url = Helpers.GetConfigKeyValue(configs, "InterfaceUrl", "CAS", null);

                        httpClient = new HttpClient();
                        httpClient.DefaultRequestHeaders.Add("clientID", clientId);
                        httpClient.DefaultRequestHeaders.Add("secret", clientKey);
                        httpClient.DefaultRequestHeaders.Accept.Add(new MediaTypeWithQualityHeaderValue("application/json"));
                        httpClient.BaseAddress = new Uri(url);
                        httpClient.Timeout = new TimeSpan(1, 0, 0);  // 1 hour timeout 

                        var jsonRequest = string.Format("$!$\"invoiceNumber\":\"{0}\",\"supplierNumber\":\"{1}\",\"supplierSiteNumber\":\"{2}\"$&$", "", "", "").Replace("$!$", "{").Replace("$&$", "}"); ;

                        HttpRequestMessage request = new HttpRequestMessage(HttpMethod.Post, "/api/CASAPRetreive/GetTransactionRecords");
                        request.Content = new StringContent(jsonRequest, Encoding.UTF8, "application/json");

                        HttpResponseMessage response = httpClient.SendAsync(request).Result;

                        if (response.StatusCode == HttpStatusCode.OK)
                        {
                            userMessage = response.Content.ReadAsStringAsync().Result;
                            if (!userMessage.Contains("SUCCEEDED"))
                            {
                                throw new InvalidPluginExecutionException(userMessage);
                            }
                        }
                        else
                            throw new InvalidPluginExecutionException(response.StatusCode.ToString() + "\r\n" + jsonRequest);
                    }
                    catch (Exception ex1)
                    {
                        Log.AppendLine("Error:" + ex1.Message);
                        isError = true;
                    }
                    finally
                    {
                        if (httpClient != null)
                            httpClient.Dispose();

                        Log.AppendLine((string)paymentEntity["vsd_name"] + " END..");
                    }
                }
            }
            catch
            {
                isError = true;
            }
            finally
            {
                Entity updateLog = new Entity(Target.LogicalName.ToLowerInvariant());
                updateLog.Id = Target.Id;
                updateLog["vsd_log"] = (Log.ToString().Length >= 4000 ? Log.ToString().Substring(0, 3999) : Log.ToString());
                OrgService.Update(updateLog);
            }

            return isError;
        }
        #endregion
    }
}
