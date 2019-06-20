using Microsoft.Extensions.Configuration;
using Microsoft.IdentityModel.Clients.ActiveDirectory;
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
    public class Authentication
    {
        public HttpClient GetHttpClient()
        {
            var builder = new ConfigurationBuilder()
                            .SetBasePath(Directory.GetCurrentDirectory())
                            .AddJsonFile("appsettings.json", optional: true, reloadOnChange: true)
                            .AddEnvironmentVariables();

            var configuration = builder.Build();
            var _dynamicsAuthenticationSettingsSection = configuration.GetSection("DynamicsAuthenticationSettings");
            var _dynamicsAuthenticationSettings = _dynamicsAuthenticationSettingsSection.Get<DynamicsAuthenticationSettings>();

            if (_dynamicsAuthenticationSettings.ActiveEnvironment.ToLower() == "onprem")
            {
                return getOnPremHttpClient(_dynamicsAuthenticationSettings.OnPremUserName, _dynamicsAuthenticationSettings.OnPremPassword,
                    _dynamicsAuthenticationSettings.OnPremDomain, _dynamicsAuthenticationSettings.OnPremWebApiUrl);
            }
            else
            {
                AuthenticationParameters ap = AuthenticationParameters.CreateFromResourceUrlAsync(
                    new Uri(_dynamicsAuthenticationSettings.CloudResourceUrl)).Result;

                String authorityUrl = ap.Authority;
                String resourceUrl = ap.Resource;

                return getOnlineHttpClient(resourceUrl, authorityUrl, _dynamicsAuthenticationSettings.CloudClientId,
                    _dynamicsAuthenticationSettings.CloudClientSecret, _dynamicsAuthenticationSettings.CloudResourceUrl,
                    _dynamicsAuthenticationSettings.CloudUserName, _dynamicsAuthenticationSettings.CloudWebApiUrl);
            }
        }

        private HttpClient getOnPremHttpClient(string userName, string password, string domainName, string webAPIBaseAddress)
        {
            var handler = new HttpClientHandler();
            handler.Credentials = new NetworkCredential(userName, password, domainName);
            //TODO: Certificate validation workaround for self-issued certificates
            handler.ClientCertificateOptions = ClientCertificateOption.Manual;
            handler.ServerCertificateCustomValidationCallback =
                (httpRequestMessage, cert, cetChain, policyErrors) =>
                {
                    return true;
                };

            HttpClient client = new HttpClient(handler);
            client.BaseAddress = new Uri(webAPIBaseAddress);
            client.Timeout = new TimeSpan(0, 2, 0);
            return client;
        }

        private HttpClient getOnlineHttpClient(string resourceURI, string authority, string clientId, string clientSecret,
            string redirectUrl, string userName, string webApiUrl)
        {
            HttpClient httpClient = new HttpClient();
            httpClient.BaseAddress = new Uri(webApiUrl);
            httpClient.Timeout = new TimeSpan(0, 2, 0);  // 2 minutes  
            httpClient.DefaultRequestHeaders.Authorization =
                new AuthenticationHeaderValue("Bearer", AcquireToken(resourceURI, authority, clientId, clientSecret, redirectUrl, userName));

            return httpClient;

        }

        private string AcquireToken(string resourceURI, string authority, string clientId, string clientSecret, string redirectUrl, string userName)
        {
            var clientCredential = new ClientCredential(clientId, clientSecret);
            AuthenticationContext authContext =
                new Microsoft.IdentityModel.Clients.ActiveDirectory.AuthenticationContext(authority, false);
            AuthenticationResult result = authContext.AcquireTokenAsync(resourceURI, clientCredential).Result;

            return result.AccessToken;
        }
    }
}
