using System;
using System.Collections.Generic;
using System.Linq;
using System.Net;
using System.Threading.Tasks;
using Ecas.Dyn365Service.Utils;
using Microsoft.AspNetCore.Http;
using Microsoft.AspNetCore.Mvc;

namespace Ecas.Dyn365Service.Controllers
{
    [Route("api/[controller]")]
    [ApiController]
    public class EcasPortalAssignmentsController : ControllerBase
    {
        // GET: api/EcasPortalAssignments
        [HttpGet]
        public ActionResult<string> Get(string contactId)
        {
            if (string.IsNullOrEmpty(contactId)) return string.Empty;

            string fetchXML = @"<fetch version='1.0' output-format='xml-platform' mapping='logical' distinct='false'>
                                  <entity name='educ_assignment'>
                                    <attribute name='statuscode' />
                                    <attribute name='educ_contact' />
                                    <attribute name='educ_assignmentid' />
                                    <order descending='false' attribute='educ_contact' />
                                    <filter type='and'>
                                      <condition attribute='statecode' operator='eq' value='0' />
                                      <condition attribute='educ_contact' operator='eq' value='{"+ contactId + @"}' />
                                    </filter>
                                    <link-entity name='educ_session' from='educ_sessionid' to='educ_session' visible='false' link-type='outer' alias='a_ee8da643bc5be911a978000d3af45d23'>
                                      <attribute name='educ_startdate' />
                                      <attribute name='educ_sessiontype' />
                                      <attribute name='educ_sessionactivity' />
                                      <attribute name='educ_locationcity' />
                                      <attribute name='educ_enddate' />
                                    </link-entity>
                                  </entity>
                                </fetch>";

            var statement = $"educ_assignments?fetchXml=" + WebUtility.UrlEncode(fetchXML);

            var response = new Dyn365WebAPI().SendRetrieveRequestAsync(statement, true);

            if (response.IsSuccessStatusCode)
            {
                return Ok(response.Content.ReadAsStringAsync().Result);
            }
            else
                return StatusCode((int)response.StatusCode,
                    $"Failed to Retrieve records: {response.ReasonPhrase}");
        }

        //// GET: api/EcasPortal/5
        //[HttpGet("{id}", Name = "Get")]
        //public string Get(int id)
        //{
        //    return "value";
        //}

        //// POST: api/EcasPortal
        //[HttpPost]
        //public void Post([FromBody] string value)
        //{
        //}

        //// PUT: api/EcasPortal/5
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
