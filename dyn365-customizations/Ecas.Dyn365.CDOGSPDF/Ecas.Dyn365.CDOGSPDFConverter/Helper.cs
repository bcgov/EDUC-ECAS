using System;
using System.Collections.Generic;
using System.Linq;
using System.Net.Http;
using System.Net.Http.Headers;
using System.Threading.Tasks;
using Microsoft.Xrm.Sdk;
using Microsoft.Xrm.Sdk.Query;

namespace Ecas.Dyn365.CDOGSPDFConverter.Helper
{
    public static class Helper
    {
        public static List<Entity> GetSystemConfigurations(IOrganizationService service, string group, string key)
        {
            List<Entity> result = new List<Entity>();

            QueryExpression exp = new QueryExpression("educ_config");
            exp.NoLock = true;
            exp.ColumnSet.AllColumns = true;
            exp.Criteria.AddCondition("statecode", ConditionOperator.Equal, 0); //Active
            if (!string.IsNullOrEmpty(group))
                exp.Criteria.AddCondition("educ_group", ConditionOperator.Equal, group);
            if (!string.IsNullOrEmpty(key))
                exp.Criteria.AddCondition("educ_key", ConditionOperator.Equal, key);

            var coll = service.RetrieveMultiple(exp);
            if (coll != null && coll.Entities != null && coll.Entities.Count > 0)
                result = coll.Entities.ToList();

            if (result.Count < 1)
                throw new InvalidPluginExecutionException(string.Format("System Configuration for Group '{0}', Key '{1}' doesn't exist..", group, key));

            return result;
        }


        public static async Task<string> GetToken(string authKey, Uri AuthUri)
        {
            var clientHandler = new HttpClientHandler();
            var client = new HttpClient(clientHandler);
            var request = new HttpRequestMessage
            {
                Method = HttpMethod.Post,
                RequestUri = AuthUri,
                Headers =
                {
                    { "cookie", "65b371b8014b36824e3b9a1c3ad5919e=41c61839c5405d223ae3c940215700b6" },
                    { "Authorization", "Basic "+ authKey},
                },
                Content = new FormUrlEncodedContent(new Dictionary<string, string>
                {
                    { "grant_type", "client_credentials" },
                }),
            };
            using (var response = await client.SendAsync(request))
            {
                response.EnsureSuccessStatusCode();
                var body = await response.Content.ReadAsStringAsync();
                return body;
            }
        }

        public static Tuple<string, string> GetContent(IOrganizationService service, Guid assignmentId)
        {
            string fileContent = null;
            string fileName = null;

            var QEannotation_objectid = assignmentId;
            var QEannotation_0_filename = "doc";
            var QEannotation_1_filename = "docx";

            var QEannotation = new QueryExpression("annotation");
            QEannotation.TopCount = 1;

            QEannotation.ColumnSet.AddColumns("filename", "filesize", "notetext", "documentbody", "isdocument", "modifiedon", "annotationid");
            QEannotation.AddOrder("modifiedon", OrderType.Descending);

            QEannotation.Criteria.AddCondition("objectid", ConditionOperator.Equal, QEannotation_objectid);
            var QEannotation_Criteria_0 = new FilterExpression();
            QEannotation.Criteria.AddFilter(QEannotation_Criteria_0);

            QEannotation_Criteria_0.FilterOperator = LogicalOperator.Or;
            QEannotation_Criteria_0.AddCondition("filename", ConditionOperator.EndsWith, QEannotation_0_filename);
            QEannotation_Criteria_0.AddCondition("filename", ConditionOperator.EndsWith, QEannotation_1_filename);

            var QEannotation_educ_assignment = QEannotation.AddLink("educ_assignment", "objectid", "educ_assignmentid");

            EntityCollection results = service.RetrieveMultiple(QEannotation);
            foreach (Entity annotation in results.Entities)
            {
                fileName = annotation["filename"].ToString();
                fileContent = annotation["documentbody"].ToString();
            }

            return Tuple.Create(fileName, fileContent);
        }


        public static async Task<byte[]> ConvertDoc(string token, string documentbody, Uri CdogsUri)
        {
            var clientHandler = new HttpClientHandler
            {
                UseCookies = false,
            };
            var client = new HttpClient(clientHandler);
            var request = new HttpRequestMessage
            {
                Method = HttpMethod.Post,
                RequestUri = CdogsUri,
                Headers =
                {
                    { "cookie", "3f799ebde5400ed01beb41d5fcfb7a36=aef13b54228074e9a0a83b9bf7bb56fa" },
                    { "Authorization", "Bearer "+token },
                },
                Content = new StringContent("{\n\t\"data\": {},\n\t\"options\": {\n\t\t\"reportName\": \"ECAS Temp Doc\",\n\t\t\"convertTo\": \"pdf\",\n\t\t\"overwrite\": true\n\t}," +
                "\n\t\"template\": {\n\t\t\"content\": \"" + documentbody + "\",\n\t\t\"encodingType\": \"base64\",\n\t\t\"fileType\": \"docx\"\n\t}\n}")
                {
                    Headers =
                    {
                        ContentType = new MediaTypeHeaderValue("application/json")
                    }
                }

            };

            using (var response = await client.SendAsync(request))
            {
                response.EnsureSuccessStatusCode();
                var body = await response.Content.ReadAsByteArrayAsync();


                return body;

            }
        }



        public static string GetConfigKeyValue(List<Entity> configurations, string key, string group)
        {
            if (string.IsNullOrEmpty(key))
                throw new InvalidPluginExecutionException("Config Key is required..");

            foreach (var configEntity in configurations)
            {
                if (configEntity["educ_key"].ToString().Equals(key, StringComparison.InvariantCultureIgnoreCase))
                {
                    bool isFinal = false;
                    if (!string.IsNullOrEmpty(group))
                    {
                        if (configEntity["educ_group"].ToString().Equals(group, StringComparison.InvariantCultureIgnoreCase))
                            isFinal = true;
                        else
                            isFinal = false;
                    }
                    else
                        isFinal = true;

                    if (isFinal)
                        return configEntity["educ_value"].ToString();
                }
            }

            throw new InvalidPluginExecutionException(string.Format("Unable to find configuration with Key '{0}', Group '{1}'..", key, group));
        }
    }
}