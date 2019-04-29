using System;
using System.Collections.Generic;
using System.Linq;
using System.Net.Http;
using System.Text;
using System.Threading.Tasks;

namespace Ecas.Dyn365Service.Utils
{
    public class Dyn365WebAPI
    {
        ///<summary> Sends an HTTP request to the current CRM service. </summary>  
        ///<param name="method">The HTTP method to invoke</param>  
        ///<param name="query">The HTTP query to execute (base URL is provided by client)</param>  
        ///<param name="formatted">True to include formatted values in response; default is false.</param>  
        ///<param name="maxPageSize">Number of records to display per output "page".</param>  
        ///<returns>An HTTP response message</returns>  
        ///
        public HttpResponseMessage SendRetrieveRequestAsync(string query, Boolean formatted = false, int maxPageSize = 50)
        {
            HttpRequestMessage request = new HttpRequestMessage(HttpMethod.Get, query);
            request.Headers.Add("Prefer", "odata.maxpagesize=" + maxPageSize.ToString());
            if (formatted)
                request.Headers.Add("Prefer",
                    "odata.include-annotations=OData.Community.Display.V1.FormattedValue");
            return new Authentication().GetHttpClient().SendAsync(request).Result;
        }

        public HttpResponseMessage SendCreateRequestAsync(string endPoint, string content)
        {
            return SendAsync(HttpMethod.Post, endPoint, content);
        }

        public HttpResponseMessage SendUpdateRequestAsync(string endPoint, string content)
        {
            return SendAsync(HttpMethod.Patch, endPoint, content);
        }

        private HttpResponseMessage SendAsync(HttpMethod operation, string endPoint, string content)
        {
            HttpRequestMessage request = new HttpRequestMessage(operation, endPoint);
            request.Content = new StringContent(content.ToString(), Encoding.UTF8, "application/json");
            return new Authentication().GetHttpClient().SendAsync(request).Result;
        }

        public HttpResponseMessage SendDeleteRequestAsync(string endPoint)
        {
            return new Authentication().GetHttpClient().DeleteAsync(endPoint).Result;
        }
    }
}
