using Ecas.Dyn365.CASIntegration.Plugin;
using Microsoft.Xrm.Sdk;
using Microsoft.Xrm.Sdk.Query;
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

        public Payment(IOrganizationService _organizationService, ITracingService _tracingService, 
            Guid _paymentId)
        {
            if (_organizationService == null) throw new ArgumentNullException("Organization Service cannot be null");
            if (_tracingService == null) throw new ArgumentNullException("Tracing Service Id cannot be null");
            if (_paymentId == Guid.Empty) throw new ArgumentNullException("Payment Id cannot be null");

            organizationService = _organizationService;
            tracingService = _tracingService;
            paymentId = _paymentId;

            tracingService.Trace("Loaded Payment Util");
        }

        public PaymentStatusCheckerResult VerifyAndUpdateStatus()
        {
            bool isError = false;
            var Log = new StringBuilder();
            var paymentrecord = organizationService.Retrieve("educ_payment", paymentId,
                    new ColumnSet("educ_paymentid", "educ_invoicenumber", "educ_suppliernumber", "educ_suppliersitenumber"));

            var invoiceNumber = paymentrecord.GetAttributeValue<string>("educ_invoicenumber");
            var supplierNumber = paymentrecord.GetAttributeValue<string>("educ_suppliernumber");
            var supplierSiteNumber = paymentrecord.GetAttributeValue<string>("educ_suppliersitenumber");

            HttpClient httpClient = null;
            try
            {
                if (string.IsNullOrEmpty(invoiceNumber)) Log.AppendLine("Invoice Number cannot be null");
                if (string.IsNullOrEmpty(supplierNumber)) Log.AppendLine("Supplier Number cannot be null");
                if (string.IsNullOrEmpty(supplierSiteNumber)) Log.AppendLine("Supplier Site Number Id cannot be null");

                if (Log.Length > 0) throw new InvalidPluginExecutionException(Log.ToString());

                Log.AppendLine("\r\nOUTPUT PARAMETERS:");
                var configs = Helpers.GetSystemConfigurations(organizationService, "CAS-AP", string.Empty);

                string userMessage = string.Empty;

                string clientKey = Helpers.GetConfigKeyValue(configs, "ClientKey", "CAS-AP");
                string clientId = Helpers.GetConfigKeyValue(configs, "ClientId", "CAS-AP");
                string url = Helpers.GetConfigKeyValue(configs, "InterfaceUrl", "CAS-AP");

                httpClient = new HttpClient();
                httpClient.DefaultRequestHeaders.Add("clientID", clientId);
                httpClient.DefaultRequestHeaders.Add("secret", clientKey);
                httpClient.DefaultRequestHeaders.Accept.Add(new MediaTypeWithQualityHeaderValue("application/json"));
                httpClient.BaseAddress = new Uri(url);
                httpClient.Timeout = new TimeSpan(1, 0, 0);  // 1 hour timeout 

                var jsonRequest = string.Format("$!$\"invoiceNumber\":\"{0}\",\"supplierNumber\":\"{1}\",\"supplierSiteNumber\":\"{2}\"$&$",
                    invoiceNumber, supplierNumber, supplierSiteNumber).Replace("$!$", "{").Replace("$&$", "}");

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
                    else
                    {
                        //Payment Processed By ECAS
                        paymentrecord["statuscode"] = new OptionSetValue(610410005);
                        organizationService.Update(paymentrecord);
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

                Log.AppendLine((string)paymentrecord["educ_name"] + " END..");

                paymentrecord["educ_casresponse"] = Log.ToString();
            }

            return new PaymentStatusCheckerResult { Success = !isError, Message = Log.ToString() };
        }
    }
}
