using Microsoft.Extensions.Options;
using Microsoft.Identity.Client;
using System;
using System.Collections.Generic;
using System.Configuration;
using System.IO;
using System.Linq;
using System.Net;
using System.Net.Http;
using System.Net.Http.Headers;
using System.Threading.Tasks;


namespace Ecas.Dyn365Service.Utils
{
    /// <summary>
    /// Registered as a singleton so one HttpClient and one MSAL client (with its token cache) serve every Dynamics call.
    /// </summary>
    public class Authentication
    {
        private readonly HttpClient _httpClient;
        private readonly IConfidentialClientApplication _confidentialClientApplication;
        private readonly string[] _scopes;

        public Authentication(IOptions<DynamicsAuthenticationSettings> dynamicsAuthenticationSettingsOptions)
        {
            var dynamicsAuthenticationSettings = dynamicsAuthenticationSettingsOptions.Value;

            if (dynamicsAuthenticationSettings.ActiveEnvironment.ToLower() == "onprem")
            {
                _httpClient = getOnPremHttpClient(dynamicsAuthenticationSettings.OnPremUserName, dynamicsAuthenticationSettings.OnPremPassword,
                    dynamicsAuthenticationSettings.OnPremDomain, dynamicsAuthenticationSettings.OnPremWebApiUrl);
            }
            else
            {
                _scopes = new[] { dynamicsAuthenticationSettings.CloudBaseUrl + "/.default" };
                _confidentialClientApplication = ConfidentialClientApplicationBuilder.Create(clientId: dynamicsAuthenticationSettings.CloudClientId)
                                                      .WithClientSecret(clientSecret: dynamicsAuthenticationSettings.CloudClientSecret)
                                                      .WithAuthority(new Uri($"https://login.microsoftonline.com/{dynamicsAuthenticationSettings.TenantId}"))
                                                      .Build();
                _httpClient = getOnlineHttpClient(dynamicsAuthenticationSettings.CloudWebApiUrl);
            }
        }

        public async Task<HttpResponseMessage> SendAsync(HttpRequestMessage request)
        {
            if (_confidentialClientApplication != null)
            {
                // Served from MSAL's in-memory cache until the token is close to expiry.
                var result = await _confidentialClientApplication.AcquireTokenForClient(_scopes).ExecuteAsync();
                request.Headers.Authorization = new AuthenticationHeaderValue("Bearer", result.AccessToken);
            }

            return await _httpClient.SendAsync(request);
        }

        private HttpClient getOnPremHttpClient(string userName, string password, string domainName, string webAPIBaseAddress)
        {

            var client = new HttpClient(new HttpClientHandler()
            {
                ServerCertificateCustomValidationCallback = (message, cert, chain, errors) => { return true; }
            });

            byte[] bytes = System.Text.Encoding.UTF8.GetBytes(domainName + "\\" + userName + ":" + password);
            string base64 = System.Convert.ToBase64String(bytes);
            client.BaseAddress = new Uri(webAPIBaseAddress);
            client.Timeout = new TimeSpan(1, 0, 0); // 1 hour timeout
            client.DefaultRequestHeaders.Add("Authorization", "Basic " + base64);
            client.DefaultRequestHeaders.Add("OData-MaxVersion", "4.0");
            client.DefaultRequestHeaders.Add("OData-Version", "4.0");
            client.DefaultRequestHeaders.Accept.Add(new System.Net.Http.Headers.MediaTypeWithQualityHeaderValue("application/json"));


            //var handler = new HttpClientHandler();
            //handler.Credentials = new NetworkCredential(userName, password, domainName);
            //handler.ClientCertificateOptions = ClientCertificateOption.Manual;
            //handler.ServerCertificateCustomValidationCallback =
            //    (httpRequestMessage, cert, cetChain, policyErrors) =>
            //    {
            //        return true;
            //    };

            //HttpClient client = new HttpClient(handler);
            //client.BaseAddress = new Uri(webAPIBaseAddress);
            //client.Timeout = new TimeSpan(0, 2, 0);
            return client;
        }


        //private HttpClient getOnlineHttpClient(string resourceURI, string authority, string clientId, string clientSecret,
        //   string redirectUrl, string userName, string webApiUrl, string tenantId)
        //{

        //    var accessToken = GetAccessToken(resourceURI, clientId, clientSecret, tenantId);

        //    HttpClient httpClient = new HttpClient();
        //    httpClient.BaseAddress = new Uri(webApiUrl);
        //    httpClient.Timeout = new TimeSpan(0, 2, 0);  // 2 minutes
        //    httpClient.DefaultRequestHeaders.Authorization =
        //    new AuthenticationHeaderValue("Bearer", accessToken);

        //    // new AuthenticationHeaderValue("Bearer", AcquireToken(resourceURI, authority, clientId, clientSecret, redirectUrl, userName));

        //    return httpClient;

        //}

        //private string AcquireToken(string resourceURI, string authority, string clientId, string clientSecret, string redirectUrl, string userName)
        //{
        //    var clientCredential = new ClientCredential(clientId, clientSecret);
        //    AuthenticationContext authContext =
        //        new Microsoft.IdentityModel.Clients.ActiveDirectory.AuthenticationContext(authority, false);
        //    AuthenticationResult result = authContext.AcquireTokenAsync(resourceURI, clientCredential).Result;

        //    return result.AccessToken;
        //}

        private HttpClient getOnlineHttpClient(string webApiUrl)
        {
            // The client lives for the whole process, so recycle connections to pick up DNS changes.
            HttpClient httpClient = new HttpClient(new SocketsHttpHandler { PooledConnectionLifetime = TimeSpan.FromMinutes(5) });
            httpClient.BaseAddress = new Uri(webApiUrl);
            httpClient.Timeout = new TimeSpan(0, 2, 0);  // 2 minutes

            return httpClient;
        }

    }
}
