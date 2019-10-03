using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.ServiceModel;
using System.Net;
using System.Net.Http;
using System.Net.Http.Headers;
using Newtonsoft.Json.Linq;

namespace Ecas.Dyn365.CAS.ScheduledJob.ScheduleJobSession
{
    public class CheckPaymentStatusLogic
    {

        public bool VerifyStatusOfInProgressPayments()
        {
            foreach (var item in GetProcessingCASPaymentPayment())
            {

            }

            return true;
        }

        private List<Guid> GetProcessingCASPaymentPayment()
        {
            var webapiurl = @"https://localhost:44331/api/";

            var query =
                //string.Format("${webapiurl}/api/operations?statement=educ_payments&$select=educ_paymentid,educ_invoicenumber,educ_suppliernumber,educ_suppliersitenumber&$filter=statuscode eq 610410006");
                string.Format("operations?statement=educ_payments&$select=educ_paymentid&$filter=statuscode eq 1",
                webapiurl);
            HttpRequestMessage request = new HttpRequestMessage(HttpMethod.Get, query);
            //request.Content = new StringContent(query.ToString(), Encoding.UTF8, "application/json");
            request.Headers.Add("Prefer", "odata.maxpagesize=5000");

            HttpResponseMessage response = getHttpClient(webapiurl).SendAsync(request).Result;
            if (response.IsSuccessStatusCode) //200
            {
                var r = response.Content.ReadAsStringAsync().Result.TrimStart(new char[] { '[' }).TrimEnd(new char[] { ']' });
                var results = JObject.Parse(r);
            }
            else
            {
                throw new Exception(string.Format("Failed to retrieve payments", response.Content));
            }

            return new List<Guid>();
        }



        private HttpClient getHttpClient(string webAPIBaseAddress)
        {
            string userName = "";
            string password = "";

            var client = new HttpClient(new HttpClientHandler()
            {
                ServerCertificateCustomValidationCallback = (message, cert, chain, errors) => { return true; }
            });
            
            byte[] bytes = System.Text.Encoding.UTF8.GetBytes(userName + ":" + password);
            string base64 = System.Convert.ToBase64String(bytes);
            client.BaseAddress = new Uri(webAPIBaseAddress);
            client.Timeout = new TimeSpan(1, 0, 0); // 1 hour timeout
            client.DefaultRequestHeaders.Add("Authorization", "Basic " + base64);
            client.DefaultRequestHeaders.Add("OData-MaxVersion", "4.0");
            client.DefaultRequestHeaders.Add("OData-Version", "4.0");
            client.DefaultRequestHeaders.Accept.Add(new System.Net.Http.Headers.MediaTypeWithQualityHeaderValue("application/json"));

            return client;
        }


    }
}
