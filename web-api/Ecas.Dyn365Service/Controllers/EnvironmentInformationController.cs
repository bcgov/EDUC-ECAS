using System;
using System.Collections.Generic;
using System.IO;
using System.Linq;
using System.Threading.Tasks;
using Ecas.Dyn365Service.Utils;
using Microsoft.AspNetCore.Http;
using Microsoft.AspNetCore.Mvc;
using Microsoft.Extensions.Configuration;

namespace Ecas.Dyn365Service.Controllers
{
    /// <summary>
    /// Controller is used to indicate the health/availability of the service. 
    /// </summary>
    [Route("api/[controller]")]
    [ApiController]
    public class EnvironmentInformation : ControllerBase
    {
        // GET: api/EnvironmentInformation
        [HttpGet]
        public ActionResult<string> Get()
        {
            var builder = new ConfigurationBuilder()
                           .SetBasePath(Directory.GetCurrentDirectory())
                           .AddJsonFile("appsettings.json", optional: true, reloadOnChange: true)
                           .AddEnvironmentVariables();

            var configuration = builder.Build();
            var _dynamicsAuthenticationSettingsSection = configuration.GetSection("DynamicsAuthenticationSettings");
            var _dynamicsAuthenticationSettings = _dynamicsAuthenticationSettingsSection.Get<DynamicsAuthenticationSettings>();

            _dynamicsAuthenticationSettings.CloudClientId = "*********";
            _dynamicsAuthenticationSettings.CloudClientSecret = "*********";
            _dynamicsAuthenticationSettings.OnPremPassword = "*********";
            var settings = Newtonsoft.Json.JsonConvert.SerializeObject(_dynamicsAuthenticationSettings);

            return Ok(settings);
        }

    }
}
