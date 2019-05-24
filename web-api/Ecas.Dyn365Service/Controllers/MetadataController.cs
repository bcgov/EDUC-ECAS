using System;
using System.Collections.Generic;
using System.Linq;
using System.Threading.Tasks;
using Ecas.Dyn365Service.Utils;
using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Http;
using Microsoft.AspNetCore.Mvc;
using Newtonsoft.Json.Linq;

namespace Ecas.Dyn365Service.Controllers
{
    /// <summary>
    /// Wrapper that executes GET (Read) on Dynamics 365 Metadata. This is to be used for querying entity attribute definition such as OptionSets or StatusReason fields.
    /// </summary>
    [Route("api/[controller]")]
    [Authorize]
    [ApiController]
    public class MetadataController : ControllerBase
    {
        DynamicsAuthenticationSettings _dynamicsAuthenticationSettings;

        public MetadataController(DynamicsAuthenticationSettings dynamicsAuthenticationSettings)
        {
            _dynamicsAuthenticationSettings = dynamicsAuthenticationSettings;
        }

        /// <summary>
        /// Executes GET operations against the Dyn365 API. View https://docs.microsoft.com/en-us/powerapps/developer/common-data-service/webapi/query-metadata-web-api
        /// </summary>
        /// <param name="entityName">Name of the Entity where attribute resides</param>
        /// <param name="optionSetName">name of the Attribute</param>
        /// <returns></returns>
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
    }
}
