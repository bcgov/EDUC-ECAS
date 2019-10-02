using Ecas.Dyn365.CASIntegration.Plugin;
using Microsoft.Xrm.Sdk;
using System;
using System.Collections.Generic;
using System.Linq;
using System.Net;
using System.Net.Http;
using System.Net.Http.Headers;
using System.Text;
using System.Threading.Tasks;

namespace Ecas.Dyn365.CASIntegration.Workflows.Utils
{
    public class Payment
    {
        Guid paymentId;
        IOrganizationService organizationService;
        ITracingService tracingService;

        public Payment(Guid _paymentId, IOrganizationService _organizationService, ITracingService _tracingService)
        {
            if (_paymentId == Guid.Empty) throw new ArgumentNullException("Payment Id cannot be null");
            if (_organizationService == null) throw new ArgumentNullException("Organization Service cannot be null");
            if (_tracingService == null) throw new ArgumentNullException("Tracing Service Id cannot be null");

            paymentId = _paymentId;
            organizationService = _organizationService;
            tracingService = _tracingService;

            tracingService.Trace("Loaded Payment Util");
        }

        public PaymentStatusCheckerResult VerifyStatus()
        {
            bool isError = false;
            var Log = new StringBuilder();

            try
            {
                Log.AppendLine("\r\nOUTPUT PARAMETERS:");
                var configs = Helpers.GetSystemConfigurations(organizationService, "CAS-AP"", string.Empty);

                string userMessage = string.Empty;
                HttpClient httpClient = null;
                try
                {
                    string clientKey = Helpers.GetConfigKeyValue(configs, "ClientKey", "CAS-AP"");
                    string clientId = Helpers.GetConfigKeyValue(configs, "ClientId", "CAS-AP"");
                    string url = Helpers.GetConfigKeyValue(configs, "InterfaceUrl", "CAS-AP"");

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

            return new PaymentStatusCheckerResult { Success = !isError, Message = Log.ToString()  };
        }
    }
}
