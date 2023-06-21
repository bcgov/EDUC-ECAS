using System;
using System.Collections.Generic;
using System.Linq;
using System.Net;
using System.Threading.Tasks;
using Ecas.Dyn365Service.Utils;
using Microsoft.AspNetCore.Authorization;
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
                                    <attribute name='educ_assignmentid' />
                                    <attribute name='educ_contractstage' />
                                    <order descending='false' attribute='educ_contact' />
                                    <filter type='and'>
                                        <filter type='or'>
                                          <condition attribute='statuscode' operator='eq' value='610410002' />
                                          <condition attribute='statuscode' operator='eq' value='610410003' />
                                        </filter>
                                      <condition attribute='statecode' operator='eq' value='0' />
                                      <condition attribute='educ_contact' operator='eq' value='{" + contactId + @"}' />
                                    </filter>
                                    <link-entity name='educ_session' from='educ_sessionid' to='educ_session' visible='false' link-type='outer' alias='session'>
                                      <attribute name='educ_startdate' alias='startDate' />
                                      <attribute name='educ_enddate' alias='endDate'/>
                                      <attribute name='educ_sessiontype' alias='sessionType' />
                                      <attribute name='educ_locationcity' alias='locationCity'/>
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
    }
}
