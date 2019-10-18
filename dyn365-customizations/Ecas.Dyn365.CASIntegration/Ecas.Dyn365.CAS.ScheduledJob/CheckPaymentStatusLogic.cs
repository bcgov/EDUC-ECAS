using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.ServiceModel;
using System.Net;
using System.Net.Http;
using System.Net.Http.Headers;
using System.Runtime.Serialization.Json;
using System.Text.RegularExpressions;

namespace Ecas.Dyn365.CAS.ScheduledJob.ScheduleJobSession
{
    public class CheckPaymentStatusLogic
    {
        string webapiurl = string.Empty;
        string userName = "ecasadmin";
        string password = "Ec@s201p!";

        public CheckPaymentStatusLogic()
        {
            webapiurl = @"https://localhost:44331/api/";
        }

        public string VerifyStatusOfInProgressPayments()
        {
            StringBuilder log = new StringBuilder();

            foreach (var paymentId in GetProcessingCASPaymentPayment())
            {
                try
                {
                    log.AppendLine($"PaymentId current processing status : {CheckPaymentStatus(paymentId)}");
                }
                catch (Exception ex)
                {
                    log.AppendLine($"PaymentId failed : {ex.Message}");
                }
                
            }

            return log.ToString();
        }

        private List<Guid> GetProcessingCASPaymentPayment()
        {
            List<Guid> paymentRecords = new List<Guid>();

            //Query Payments Sent to ECAS
            var query =
                string.Format("operations?statement=educ_payments&$select=educ_paymentid&$filter=statuscode eq 610410006",
                webapiurl);
            HttpRequestMessage request = new HttpRequestMessage(HttpMethod.Get, query);
            request.Content = new StringContent(query.ToString(), Encoding.UTF8, "application/json");
            request.Headers.Add("Prefer", "odata.maxpagesize=5000");

            HttpResponseMessage response = getHttpClient(webapiurl).GetAsync(query.ToString()).Result;
            if (response.IsSuccessStatusCode) //200
            {
                var r = response.Content.ReadAsStringAsync().Result;
                MatchCollection guids = Regex.Matches(r, @"(\{){0,1}[0-9a-fA-F]{8}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{12}(\}){0,1}"); //Match all substrings in findGuid
                for (int i = 0; i < guids.Count; i++)
                {
                    paymentRecords.Add(new Guid(guids[i].Value));
                }
            }
            else
            {
                throw new Exception(string.Format("Failed to retrieve payments", response.Content));
            }

            return paymentRecords;
        }

        private string CheckPaymentStatus(Guid paymentId)
        {
            var endpoint = string.Format("action?name=VerifyAndUpdateCasPaymentStatus",
                webapiurl);
            string action = "{'educ_payment': '" + paymentId.ToString() + "'}";
            HttpRequestMessage request = new HttpRequestMessage(HttpMethod.Post, endpoint);
            request.Content = new StringContent(action, Encoding.UTF8, "application/json");

            HttpResponseMessage response = getHttpClient(webapiurl).SendAsync(request).Result;

            if (response.IsSuccessStatusCode) //200
            {
                return response.Content.ReadAsStringAsync().Result;
            }
            else
            {
                throw new Exception(string.Format("Failed to invoke VerifyCasPaymentStatus", response.Content));
            }
        }

        private HttpClient getHttpClient(string webAPIBaseAddress)
        {
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
