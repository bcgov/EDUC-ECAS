using Microsoft.IdentityModel.Clients.ActiveDirectory;
using System;
using System.Collections.Generic;
using System.Linq;
using System.Net;
using System.Net.Http;
using System.Net.Http.Headers;
using System.Threading.Tasks;

namespace Ecas.Dyn365Service.Utils
{
    public class Authentication
    {
        private HttpClient getOnPremHttpClient(string userName, string password, string domainName, string webAPIBaseAddress)
        {
            HttpClient client = new HttpClient(new HttpClientHandler() { Credentials = new NetworkCredential(userName, password, domainName) });
            client.BaseAddress = new Uri(webAPIBaseAddress);
            client.Timeout = new TimeSpan(0, 2, 0);
            return client;
        }

        private HttpClient getOnlineHttpClient(string resourceURI, string authority, string clientId, string redirectUrl, string userName)
        {
            HttpClient httpClient = new HttpClient();
            httpClient.BaseAddress = new Uri("https://ecasbc.api.crm3.dynamics.com/api/data/v9.1/");
            httpClient.Timeout = new TimeSpan(0, 2, 0);  // 2 minutes  
            httpClient.DefaultRequestHeaders.Authorization =
                new AuthenticationHeaderValue("Bearer", AcquireToken(resourceURI, authority, clientId, redirectUrl, userName));

            return httpClient;

        }

        public HttpClient GetHttpClient()
        {
            AuthenticationParameters ap = AuthenticationParameters.CreateFromResourceUrlAsync(
                        new Uri("https://ecasbc.crm3.dynamics.com/api/data/")).Result;

            String authorityUrl = ap.Authority;
            String resourceUrl = ap.Resource;

            //return getOnPremHttpClient("vidantas", "Usabrasil24", "IDIR", "https://lucifer.idir.bcgov/ECAS/api/data/v9.0/");
            return getOnlineHttpClient(resourceUrl, authorityUrl, "ec8867d2-fa01-4fa3-8d45-f23aeddf3449", "http://localhost", "admin@ecasbc.onmicrosoft.com");
        }

        private string AcquireToken(string resourceURI, string authority, string clientId, string redirectUrl, string userName)
        {
            // TODO Substitute your correct CRM root service address,   
            //string resource = "https://mydomain.crm.dynamics.com";

            // TODO Substitute your app registration values that can be obtained after you  
            // register the app in Active Directory on the Microsoft Azure portal.  
            //string clientId = "e5cf0024-a66a-4f16-85ce-99ba97a24bb2";
            //string redirectUrl = "http://localhost/";

            // Authenticate the registered application with Azure Active Directory.  
            //var parameters = new PlatformParameters();
            var clientCredential = new ClientCredential(clientId, "rd2Ia9x+3j9HcQuI81m3ihr5dnEtZ8b+kM5sPYCFv2I=");
            AuthenticationContext authContext =
                new Microsoft.IdentityModel.Clients.ActiveDirectory.AuthenticationContext(authority, false);
            AuthenticationResult result = authContext.AcquireTokenAsync(resourceURI, clientCredential).Result;

            return result.AccessToken;
        }
    }
}
