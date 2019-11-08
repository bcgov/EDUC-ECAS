using System;
using System.Collections.Generic;
using System.Linq;
using System.Threading.Tasks;
using Microsoft.AspNetCore.Http;
using Microsoft.AspNetCore.Mvc;
using Ecas.Dyn365Service.Utils;

namespace Ecas.Dyn365Service.Controllers
{
    [Route("api/[controller]")]
    [ApiController]
    public class ActionController : ControllerBase
    {

        /// <summary>
        /// Executes POST operations against the Dyn365 API. View https://docs.microsoft.com/en-us/powerapps/developer/common-data-service/webapi/create-entity-web-api
        /// </summary>
        /// <param name="statement">Requested Operation statement</param>
        /// <param name="value">Json schema with values to be used in the operation</param>
        /// <returns></returns>
        // POST: api/Operations
        [HttpPost]
        public ActionResult<string> Post(string name, [FromBody]dynamic value)
        {
            var response = new Dyn365WebAPI().SendCreateRequestAsync(name, value.ToString());

            //TODO: Improve Exception handling
            if (response.IsSuccessStatusCode)
            {
                var result = response.Content.ReadAsStringAsync().Result;
                return Ok($"{result}");
            }
            else
            {
                return StatusCode((int)response.StatusCode,
                    $"Failed to Create record: {response.ReasonPhrase}");
            }
        }


    }
}
