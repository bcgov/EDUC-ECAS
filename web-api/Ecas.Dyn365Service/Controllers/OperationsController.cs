using System;
using System.Collections.Generic;
using System.IO;
using System.Linq;
using System.Net;
using System.Net.Http;
using System.Runtime.Serialization.Json;
using System.Text.RegularExpressions;
using System.Threading.Tasks;
using Ecas.Dyn365Service.Utils;
using Microsoft.AspNetCore.Http;
using Microsoft.AspNetCore.Mvc;
using Newtonsoft.Json;
using Newtonsoft.Json.Linq;

namespace Ecas.Dyn365Service.Controllers
{
    [Route("api/[controller]")]
    [ApiController]
    public class OperationsController : ControllerBase
    {
        // GET: api/Operations
        [HttpGet]
        public ActionResult<string> Get(string statement)
        {
            if (string.IsNullOrEmpty(statement)) return string.Empty;

            if (Request.QueryString.Value.IndexOf("&") > 0)
            {
                var filters = Request.QueryString.Value.Substring(Request.QueryString.Value.IndexOf("&") + 1);
                statement = $"{statement}?{filters}";
            }

            var response = new Dyn365WebAPI().SendRetrieveRequestAsync(statement, true);

            //TODO: Improve Exception handling
            if (response.IsSuccessStatusCode)
                return Ok(response.Content.ReadAsStringAsync().Result);
            else
                return StatusCode((int)response.StatusCode,
                    $"Failed to Retrieve records: {response.ReasonPhrase}");
        }

        // POST: api/Operations
        [HttpPost]
        public ActionResult<string> Post(string statement, [FromBody]dynamic value)
        {
            var response = new Dyn365WebAPI().SendCreateRequestAsync(statement, value.ToString());

            //TODO: Improve Exception handling
            if (response.IsSuccessStatusCode)
            {
                var entityUri = response.Headers.GetValues("OData-EntityId")[0];
                string pattern = @"(\{){0,1}[0-9a-fA-F]{8}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{12}(\}){0,1}";
                Match m = Regex.Match(entityUri, pattern, RegexOptions.IgnoreCase);
                var newRecordId = string.Empty;
                if (m.Success) { newRecordId = m.Value; return Ok($"{newRecordId}"); }
                else return StatusCode((int)HttpStatusCode.InternalServerError, 
                    "Unable to create record at this time");
            }

            else
                return StatusCode((int)response.StatusCode,
                    $"Failed to Create record: {response.ReasonPhrase}");
        }

        // POST: api/Operations
        [HttpPatch]
        public ActionResult<string> Patch(string statement, [FromBody] dynamic value)
        {
            HttpResponseMessage response = new Dyn365WebAPI().SendUpdateRequestAsync(statement, value.ToString());

            //TODO: Improve Exception handling
            if (response.IsSuccessStatusCode)
                return Ok($"{value.ToString()}");
            else
                return StatusCode((int)response.StatusCode,
                    $"Failed to Update record: {response.ReasonPhrase}");
        }

        // DELETE: api/ApiWithActions/5
        [HttpDelete]
        public ActionResult<string> Delete(string statement)
        {
            var response = new Dyn365WebAPI().SendDeleteRequestAsync(statement);

            //TODO: Improve Exception handling
            if (response.IsSuccessStatusCode)
                return Ok($"{statement} removed");
            else
                return StatusCode((int)response.StatusCode,
                    response.Content.ToString());
        }
    }
}
