using System;
using System.Collections.Generic;
using System.Linq;
using System.Net.Http;
using System.Text;
using System.Threading.Tasks;
using Ecas.Dyn365Service.Utils;
using Microsoft.AspNetCore.Http;
using Microsoft.AspNetCore.Mvc;
using Microsoft.IdentityModel.Clients.ActiveDirectory;
using Newtonsoft.Json.Linq;

namespace Ecas.Dyn365Service.Controllers
{
    [Route("api/[controller]")]
    [ApiController]
    public class CustomActionController : ControllerBase
    {
        // GET: api/CustomAction
        [HttpGet]
        public IEnumerable<string> Get()
        {

            AuthenticationParameters ap = AuthenticationParameters.CreateFromResourceUrlAsync(
                        new Uri("https://ecasbc.crm3.dynamics.com/api/data/")).Result;

            String authorityUrl = ap.Authority;
            String resourceUrl = ap.Resource;

            JObject contact1 = new JObject();
            contact1.Add("firstname", "Peter");
            contact1.Add("lastname", "Cambel");
            contact1.Add("annualincome", 80000);
            contact1["jobtitle"] = "Junior Developer";

            var httpClient = new Authentication().getOnPremHttpClient("vdantas", "", "IDIR", "https://dangle.dev.jag.gov.bc.ca/LCRB-CLLCMS-DEV/api/data/v8.2/");
            //var httpClient = new Authentication().getOnlineHttpClient(resourceUrl, authorityUrl, "ec8867d2-fa01-4fa3-8d45-f23aeddf3449", "http://localhost", "admin@ecasbc.onmicrosoft.com");

            HttpRequestMessage request = new HttpRequestMessage(HttpMethod.Post, "contacts");
            request.Content = new StringContent(contact1.ToString(), Encoding.UTF8, "application/json");
            HttpResponseMessage response = httpClient.SendAsync(request).Result;

            return new string[] { "value1", "value2" };
        }

        // GET: api/CustomAction/5
        [HttpGet("{id}", Name = "Get")]
        public string Get(int id)
        {
            return "value";
        }

        // POST: api/CustomAction
        [HttpPost]
        public void Post([FromBody] string value)
        {
        }

        // PUT: api/CustomAction/5
        [HttpPut("{id}")]
        public void Put(int id, [FromBody] string value)
        {
        }

        // DELETE: api/ApiWithActions/5
        [HttpDelete("{id}")]
        public void Delete(int id)
        {
        }
    }
}
