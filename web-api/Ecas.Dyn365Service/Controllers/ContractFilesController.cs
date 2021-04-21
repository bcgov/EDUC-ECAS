using System;
using System.Linq;
using System.Net;
using System.Text.RegularExpressions;
using Ecas.Dyn365Service.Utils;
using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using Newtonsoft.Json.Linq;

namespace Ecas.Dyn365Service.Controllers
{
    [Route("api/[controller]/[action]")]
    [Authorize]
    [ApiController]
    public class ContractFilesController : ControllerBase
    {
        [HttpGet]
        [ActionName("GetFile")]
         public ActionResult<string> GetFile(string annotationId)
        {
            if (string.IsNullOrEmpty(annotationId)) return string.Empty;
            string fetchXML = @"<fetch version='1.0' output-format='xml-platform' mapping='logical' distinct='false'>
                                  <entity name='annotation' >
                                    <attribute name='filename' />
                                    <attribute name='filesize' />
                                    <attribute name='notetext' />
                                    <attribute name='documentbody' />
                                    <filter>
                                      <condition attribute='annotationid' operator='eq' value= '{" + annotationId + @"}' />
                                    </filter>
                                  </entity>
                                </fetch>";

            var statement = $"annotations?fetchXml=" + WebUtility.UrlEncode(fetchXML);
            var response = new Dyn365WebAPI().SendRetrieveRequestAsync(statement, true);
            if (response.IsSuccessStatusCode)
            {
                return Ok(response.Content.ReadAsStringAsync().Result);
            }
            else
                return StatusCode((int)response.StatusCode,
                    $"Failed to Retrieve records: {response.ReasonPhrase}");
        }


        [HttpGet]
        [ActionName("ContractFile")]
        public ActionResult<string> ContractFile(string assignmentId)
        {
            if (string.IsNullOrEmpty(assignmentId)) return string.Empty;
            string fetchXML = @"<fetch version='1.0' output-format='xml-platform' mapping='logical' distinct='false' top='1'>
                                  <entity name='annotation' >
                                    <attribute name='filename' />
                                    <attribute name='filesize' />
                                    <attribute name='notetext' />
                                    <filter>
                                      <condition attribute='filename' operator='ends-with' value='.pdf' />
                                      <condition attribute='objecttypecode' operator='eq' value='10038' />
                                      <condition attribute='notetext' operator='neq' value='Uploaded Document' />
                                      <condition attribute='objectid' operator='eq' value= '{" + assignmentId + @"}' />
                                    </filter>
                                  <order attribute='createdon' descending='true' />
                                  <link-entity name='educ_assignment' from='educ_assignmentid' to='objectid'>
                                      <filter>
                                        <condition attribute='educ_contractstage' operator='eq' value='610410003' />
                                      </filter>
                                    </link-entity>
                                  </entity>
                                </fetch>";

            var statement = $"annotations?fetchXml=" + WebUtility.UrlEncode(fetchXML);
            var response = new Dyn365WebAPI().SendRetrieveRequestAsync(statement, true);

            if (response.IsSuccessStatusCode)
            {
                return Ok(response.Content.ReadAsStringAsync().Result);
            }
            else
                return StatusCode((int)response.StatusCode,
                    $"Failed to Retrieve records: {response.ReasonPhrase}");
        }


        [HttpGet]
        [ActionName("ListUploadedFiles")]
        public ActionResult<string> ListUploadedFiles(string assignmentId)
        {
            if (string.IsNullOrEmpty(assignmentId)) return string.Empty;
           string fetchXML = @"<fetch version='1.0' output-format='xml-platform' mapping='logical' distinct='false'>
                                  <entity name='annotation' >
                                    <attribute name='filename' />
                                    <attribute name='filesize' />
                                    <attribute name='notetext' />
                                    <filter>
                                      <condition attribute='objecttypecode' operator='eq' value='10038' />
                                      <condition attribute='notetext' operator='eq' value='Uploaded Document' />
                                      <condition attribute='objectid' operator='eq' value= '{" + assignmentId + @"}' />
                                    </filter>
                                  <link-entity name='educ_assignment' from='educ_assignmentid' to='objectid'>
                                      <filter>
                                        <condition attribute='educ_contractstage' operator='eq' value='610410003' />
                                      </filter>
                                    </link-entity>
                                  </entity>
                                </fetch>";

            var statement = $"annotations?fetchXml=" + WebUtility.UrlEncode(fetchXML);
            var response = new Dyn365WebAPI().SendRetrieveRequestAsync(statement, true);

            if (response.IsSuccessStatusCode)
            {
                return Ok(response.Content.ReadAsStringAsync().Result);
            }
            else
                return StatusCode((int)response.StatusCode,
                    $"Failed to Retrieve records: {response.ReasonPhrase}");
        }

        [HttpPost]
        [ActionName("UploadFile")]
         public ActionResult<string> UploadFile([FromBody]dynamic value)
        {
            var rawJsonData = value.ToString();

            // stop, if the file size exceeds 3mb 
            if (rawJsonData.Length > 3999999) { return StatusCode((int)HttpStatusCode.InternalServerError, "The file size exceeds the limit allowed (<3Mb)."); };
            JObject obj = JObject.Parse(rawJsonData);
            obj.Add("notetext", JToken.FromObject(new string("Uploaded Document")));
            rawJsonData = obj.ToString();


            string filename = (string)obj["filename"];
            string[] partialfilename = filename.Split('.');
            string fileextension = partialfilename[partialfilename.Count()-1].ToLower();

            // stop, if the file format whether is not JPG, PDF or PNG

            string[] acceptedFileFormats = { "jpg", "jpeg", "pdf", "png" };
 
            if (Array.IndexOf(acceptedFileFormats, fileextension) == -1)
            {
                      return StatusCode((int)HttpStatusCode.InternalServerError, "Sorry, only PDF, JPG and PNG file formats are supported.");
            }
            var response = new Dyn365WebAPI().SendCreateRequestAsync("annotations", rawJsonData);

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




        [HttpDelete]
        [ActionName("RemoveFile")]
        public ActionResult<string> RemoveFile(string statement)
        {
            statement = "annotations(" + statement + ")";
            var response = new Dyn365WebAPI().SendDeleteRequestAsync(statement);
            if (response.IsSuccessStatusCode)
            {
                return Ok("The contract has been removed");
            }
            else
                return StatusCode((int)response.StatusCode,
                    $"Failed to remove the contract: {response.ReasonPhrase}");
        }

    }
}
