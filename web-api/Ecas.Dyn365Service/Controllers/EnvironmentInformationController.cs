using System;
using System.Collections.Generic;
using System.IO;
using System.Linq;
using System.Threading.Tasks;
using Ecas.Dyn365Service.Utils;
using Microsoft.AspNetCore.Http;
using Microsoft.AspNetCore.Mvc;
using Microsoft.Extensions.Options;
using Newtonsoft.Json.Linq;

namespace Ecas.Dyn365Service.Controllers
{
    /// <summary>
    /// Controller is used to indicate the health/availability of the service. 
    /// </summary>
    [Route("api/[controller]")]
    [ApiController]
    public class EnvironmentInformation : ControllerBase
    {
        DynamicsAuthenticationSettings _dynamicsAuthenticationSettings;

        public EnvironmentInformation(IOptions<DynamicsAuthenticationSettings> dynamicsAuthenticationSettingsOptions)
        {
            _dynamicsAuthenticationSettings = dynamicsAuthenticationSettingsOptions.Value;
        }

        // GET: api/EnvironmentInformation
        [HttpGet]
        public ActionResult<string> Get()
        {
            // Mask a copy; the settings instance is shared with Authentication.
            var maskedSettings = JObject.FromObject(_dynamicsAuthenticationSettings);
            maskedSettings["CloudClientId"] = "*********";
            maskedSettings["CloudClientSecret"] = "*********";
            maskedSettings["OnPremPassword"] = "*********";
            var settings = Newtonsoft.Json.JsonConvert.SerializeObject(maskedSettings);

            return Ok(settings);
        }

    }
}
