using System;
using System.Collections.Generic;
using System.Linq;
using System.Threading.Tasks;
using Ecas.Dyn365Service.Utils;
using Microsoft.AspNetCore.Http;
using Microsoft.AspNetCore.Mvc;
using Newtonsoft.Json.Linq;

namespace Ecas.Dyn365Service.Controllers
{
    [Route("api/[controller]")]
    [ApiController]
    public class MetadataController : ControllerBase
    {
        DynamicsAuthenticationSettings _dynamicsAuthenticationSettings;

        public MetadataController(DynamicsAuthenticationSettings dynamicsAuthenticationSettings)
        {
            _dynamicsAuthenticationSettings = dynamicsAuthenticationSettings;
        }
        // GET: api/Metadata
        [HttpGet]
        public ActionResult<string> Get(string entityName, string optionSetName)
        {
            if (string.IsNullOrEmpty(entityName) || string.IsNullOrEmpty(optionSetName)) return string.Empty;

            var statement = string.Empty;
            if(optionSetName.ToLower() != "statuscode")
                statement = $"EntityDefinitions(LogicalName='{entityName}')/Attributes/Microsoft.Dynamics.CRM.PicklistAttributeMetadata?$select=LogicalName&$filter=LogicalName eq '{optionSetName}'&$expand=OptionSet";
            else
                statement = $"EntityDefinitions(LogicalName='{entityName}')/Attributes/Microsoft.Dynamics.CRM.StatusAttributeMetadata?$select=LogicalName&$filter=LogicalName eq '{optionSetName}'&$expand=OptionSet";

            var response = new Dyn365WebAPI().SendRetrieveRequestAsync(statement, true);

            if (response.IsSuccessStatusCode)
            {
                JObject metadataInfo = JObject.Parse(response.Content.ReadAsStringAsync().Result);
                Dynamics365OptionSet optionSet = new Dynamics365OptionSet
                {
                    InternalName = metadataInfo["value"][0]["OptionSet"]["Name"].ToString(),
                    LogicalName = metadataInfo["value"][0]["LogicalName"].ToString(),
                    Options = (from o in metadataInfo["value"][0]["OptionSet"]["Options"]
                               select new Dynamics365OptionSetItem
                               {
                                   Id = Convert.ToInt32(o["Value"]),
                                   Label = o["Label"]["LocalizedLabels"][0]["Label"].ToString()
                               }).ToList()
                };
                return Ok(JObject.FromObject(optionSet));
            }
            else
                return StatusCode((int)response.StatusCode,
                    $"Failed to Retrieve records: {response.ReasonPhrase}");
        }

        //// GET: api/Metadata/5
        //[HttpGet("{id}", Name = "Get")]
        //public string Get(int id)
        //{
        //    return "value";
        //}

        //// POST: api/Metadata
        //[HttpPost]
        //public void Post([FromBody] string value)
        //{
        //}

        //// PUT: api/Metadata/5
        //[HttpPut("{id}")]
        //public void Put(int id, [FromBody] string value)
        //{
        //}

        //// DELETE: api/ApiWithActions/5
        //[HttpDelete("{id}")]
        //public void Delete(int id)
        //{
        //}
    }
}
