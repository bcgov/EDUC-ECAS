using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.ServiceModel;
using System.Net;
using System.Net.Http;
using System.Net.Http.Headers;

namespace Ecas.Dyn365.CAS.ScheduledJob.ScheduleJobSession
{
    public class CheckPaymentStatusLogic
    {

        public bool VerifyStatusOfInProgressPayments()
        {
            var endPoint = string.Format("${}/api/operation=ecas_payments&$filter=ecas_paymentid eq ${paymentid}");
            HttpRequestMessage request = new HttpRequestMessage(HttpMethod.Post, endPoint);
            request.Content = new StringContent(content.ToString(), Encoding.UTF8, "application/json");

        }



        private HttpClient getHttpClient(string userName, string password, string webAPIBaseAddress)
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
