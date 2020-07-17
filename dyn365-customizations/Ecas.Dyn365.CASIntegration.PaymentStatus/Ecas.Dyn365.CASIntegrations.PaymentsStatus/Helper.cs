using Microsoft.Xrm.Sdk;
using Microsoft.Xrm.Sdk.Client;
using Microsoft.Xrm.Sdk.Query;
using System;
using System.Collections.Generic;
using System.Linq;
using System.Net.Http;
using System.Net.Http.Headers;
using System.ServiceModel.Description;
using System.Text;
using Ecas.Dyn365.CASIntegrations.PaymentsStatus.Models;
using Ecas.Dyn365.CASIntegrations.PaymentsStatus.StringConstants;
using Microsoft.Xrm.Sdk.Messages;

namespace Ecas.Dyn365.CASIntegration.Plugin
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

        /// <summary>
        /// 
        /// </summary>
        /// <param name="service"></param>
        /// <returns></returns>
        public static List<Entity> GetPaymentRecordsForProcessing(IOrganizationService service)
        {
            List<Entity> result = new List<Entity>();

            QueryExpression exp = new QueryExpression("educ_payment");
            exp.NoLock = true;
            exp.ColumnSet.AllColumns = true;
            //exp.ColumnSet.AddColumns(new string[] { "educ_paymentid", "educ_assignment", "educ_invoicenumber" });
            exp.Criteria.AddCondition("statecode", ConditionOperator.Equal, 0); //Active
            exp.Criteria.AddCondition("statuscode", ConditionOperator.Equal, 610410006); // Sent to CAS

            var coll = service.RetrieveMultiple(exp);

            if (coll != null && coll.Entities != null && coll.Entities.Count > 0)
                result = coll.Entities.ToList();

            //commented out below lines to handle no matching payments situation on plugin 
            //if (result.Count < 1)
            //    throw new InvalidPluginExecutionException(string.Format("Unable to fetch payment records.  "));
            
            return result;
        }


        /// <summary>
        /// Gets the related child records
        /// </summary>
        /// <param name="service"></param>
        /// <param name="parentID"></param>
        /// <param name="relatedAttribute"></param>
        /// <param name="entityName"></param>
        /// <param name="throwZeroCountException">Throw InvalidPluginException if no records were found</param>
        /// <returns></returns>
        public static List<Entity> GetRelatedChildRecords(IOrganizationService service, Guid parentID, string relatedAttribute, string entityName, bool throwZeroCountException)
        {
            List<Entity> result = new List<Entity>();

            QueryExpression exp = new QueryExpression(entityName);
            exp.NoLock = true;
            exp.ColumnSet.AllColumns = true;
            //exp.ColumnSet.AddColumns(new string[] { "educ_paymentid", "educ_assignment", "educ_invoicenumber" });
            exp.Criteria.AddCondition("statecode", ConditionOperator.Equal, 0); //Active
            exp.Criteria.AddCondition(relatedAttribute, ConditionOperator.Equal, parentID); //Fetch the related records

            var coll = service.RetrieveMultiple(exp);

            if (coll != null && coll.Entities != null && coll.Entities.Count > 0)
                result = coll.Entities.ToList();

            if (result.Count < 1 && throwZeroCountException)
                throw new InvalidPluginExecutionException(string.Format("Unable to fetch record for {0} ", entityName));

            return result;
        }

        /// <summary>
        /// 
        /// </summary>
        /// <param name="service"></param>
        /// <param name="parentID"></param>
        /// <param name="entityName"></param>
        /// <param name="columnSet"></param>
        /// <param name="throwZeroCountException">Throw InvalidPluginException if no records were found</param></param>
        /// <returns></returns>
        public static List<Entity> GetRelatedRecords(IOrganizationService service, Guid parentID, string entityName, ColumnSet columnSet, bool throwZeroCountException)
        {
            List<Entity> result = new List<Entity>();

            QueryExpression exp = new QueryExpression(entityName)
            {
                NoLock = true,
                ColumnSet = columnSet
            };

            exp.Criteria.AddCondition("statecode", ConditionOperator.Equal, 0); //Active

            var coll = service.RetrieveMultiple(exp);

            if (coll != null && coll.Entities != null && coll.Entities.Count > 0)
                result = coll.Entities.ToList();

            if (result.Count < 1 && throwZeroCountException)
                throw new InvalidPluginExecutionException(string.Format("Unable to fetch child entity records records for entity {0} ", entityName));

            return result;
        }

        /// <summary>
        /// 
        /// </summary>
        /// <param name="service"></param>
        /// <returns></returns>
        public static Entity GetContactFromPayment(IOrganizationService service, Guid paymentId)
        {
            Entity result = new Entity();

            QueryExpression exp = new QueryExpression("educ_assignment");
            exp.NoLock = true;
            exp.ColumnSet.AllColumns = true;
            exp.Criteria.AddCondition("statecode", ConditionOperator.Equal, 0); //Active
            exp.Criteria.AddCondition("educ_assignmentid", ConditionOperator.Equal, paymentId); // Get the assignment record

            //var coll = service.Retrieve(;


            if (result == null)
                throw new InvalidPluginExecutionException(string.Format("Unable to fetch payment records.  "));

            return result;
        }

        /// <summary>
        /// Returns a record by ID
        /// </summary>
        /// <param name="service"></param>
        /// <returns></returns>
        public static Entity GetEntityRecordByID(IOrganizationService service, string entitySchemaName, string primaryKey, Guid ID)
        {
            QueryExpression exp = new QueryExpression(entitySchemaName)
            {
                NoLock = true
            };
            exp.ColumnSet.AllColumns = true;
            exp.Criteria.AddCondition("statecode", ConditionOperator.Equal, 0); //Active
            exp.Criteria.AddCondition(primaryKey, ConditionOperator.Equal, ID); // Where the Primary Key equals the ID

            Entity entity = service.Retrieve(entitySchemaName, ID, new ColumnSet(true));

            if (entity == null)
                throw new InvalidPluginExecutionException(string.Format("Unable to fetch the entity record "));

            return entity;
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

        public static string GetSecureConfigKeyValue(List<Entity> configurations, string key, string group)
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
                        return configEntity["educ_securevalue"].ToString();
                }
            }

            throw new InvalidPluginExecutionException(string.Format("Unable to find configuration with Key '{0}', Group '{1}'..", key, group));
        }

        public static Entity GetSystemUserId(IOrganizationService service, string userName)
        {
            List<Entity> result = new List<Entity>();

            QueryExpression exp = new QueryExpression("systemuser");
            exp.NoLock = true;
            exp.ColumnSet.AllColumns = true;
            exp.Criteria.AddCondition("domainname", ConditionOperator.Equal, userName);


            var coll = service.RetrieveMultiple(exp);
            if (coll != null && coll.Entities != null && coll.Entities.Count > 0)
                result = coll.Entities.ToList();

            if (result.Count < 1)
                throw new InvalidPluginExecutionException($"System User {userName} cannot be found");

            return result[0];
        }

        public static IOrganizationService InitializeOrganizationService(string serverUrl, string orgName, string deploymentType, string userName, string password)
        {
            Console.WriteLine(string.Format("Initializing organization service to '{0}'", serverUrl));

            var credentials = new ClientCredentials();
            if (string.IsNullOrEmpty(userName) || string.IsNullOrEmpty(password))
                credentials.Windows.ClientCredential = System.Net.CredentialCache.DefaultNetworkCredentials;
            else
            {
                credentials.UserName.UserName = userName;
                credentials.UserName.Password = password;
            }

            Uri crmServerUrl;
            if (deploymentType.Equals("CrmOnline", StringComparison.InvariantCultureIgnoreCase))
                crmServerUrl = new Uri(string.Format("{0}/XRMServices/2011/Organization.svc", serverUrl));
            else
                crmServerUrl = new Uri(string.Format("{0}/{1}/XRMServices/2011/Organization.svc", serverUrl, orgName));

            System.Net.ServicePointManager.SecurityProtocol = System.Net.SecurityProtocolType.Tls12;

            using (OrganizationServiceProxy serviceProxy = new OrganizationServiceProxy(crmServerUrl, null, credentials, null))
            {
                serviceProxy.EnableProxyTypes();
                serviceProxy.Timeout = new TimeSpan(1, 0, 0);
                return (IOrganizationService)serviceProxy;
            }
        }

        public static IOrganizationService InitializeProxyOrganizationService(string serverUrl, string orgName, string deploymentType, string userName, string password, Guid callerId)
        {
            Console.WriteLine(string.Format("Initializing organization service to '{0}'", serverUrl));

            var credentials = new ClientCredentials();
            if (string.IsNullOrEmpty(userName) || string.IsNullOrEmpty(password))
                credentials.Windows.ClientCredential = System.Net.CredentialCache.DefaultNetworkCredentials;
            else
            {
                credentials.UserName.UserName = userName;
                credentials.UserName.Password = password;
            }

            Uri crmServerUrl;
            if (deploymentType.Equals("CrmOnline", StringComparison.InvariantCultureIgnoreCase))
                crmServerUrl = new Uri(string.Format("{0}/XRMServices/2011/Organization.svc", serverUrl));
            else
                crmServerUrl = new Uri(string.Format("{0}/{1}/XRMServices/2011/Organization.svc", serverUrl, orgName));

            System.Net.ServicePointManager.SecurityProtocol = System.Net.SecurityProtocolType.Tls12;

            using (OrganizationServiceProxy serviceProxy = new OrganizationServiceProxy(crmServerUrl, null, credentials, null))
            {
                serviceProxy.CallerId = callerId;
                serviceProxy.Timeout = new TimeSpan(4, 0, 0);
                serviceProxy.EnableProxyTypes();
                return (IOrganizationService)serviceProxy;
            }
        }

        public static DiscoveryServiceProxy InitializeDiscoveryService(string serverUrl, string orgName, string deploymentType, string userName, string password)
        {
            Console.WriteLine(string.Format("Initializing discovery service to '{0}'", serverUrl));

            var credentials = new ClientCredentials();
            if (string.IsNullOrEmpty(userName) || string.IsNullOrEmpty(password))
                credentials.Windows.ClientCredential = System.Net.CredentialCache.DefaultNetworkCredentials;
            else
            {
                credentials.UserName.UserName = userName;
                credentials.UserName.Password = password;
            }

            Uri crmServerUrl;
            if (deploymentType.Equals("CrmOnline", StringComparison.InvariantCultureIgnoreCase))
                crmServerUrl = new Uri(string.Format("{0}/XRMServices/2011/Discovery.svc", serverUrl));
            else
                crmServerUrl = new Uri(string.Format("{0}/{1}/XRMServices/2011/Discovery.svc", serverUrl, orgName));

            System.Net.ServicePointManager.SecurityProtocol = System.Net.SecurityProtocolType.Tls12;

            using (DiscoveryServiceProxy serviceProxy = new DiscoveryServiceProxy(crmServerUrl, null, credentials, null))
            {
                serviceProxy.Timeout = new TimeSpan(4, 0, 0);
                return serviceProxy;
            }
        }

        /// <summary>
        /// Creates a new record for the Cron Job Entity in Dynamics to enable next trigger of the plugin when the record is deleted by the Bulk
        /// deletion job.
        /// </summary>
        /// <param name="cronJobEntityName"></param>
        /// <param name="service"></param>
        public static void CreateCronJobSingletonRecord(string cronJobEntityName, IOrganizationService service)
        {
            Entity newCronJob = new Entity(cronJobEntityName);
            newCronJob[Payment.CAS_AP_CRON_JOB_PROXY.NAME] = Strings.SINGLETON_RECORD;
            service.Create(newCronJob);
        }

        /// <summary>
        /// Creates a new record for the Cron Job Entity in Dynamics to enable next trigger of the plugin when the record is deleted by the Bulk
        /// deletion job.
        /// </summary>
        /// <param name="cronJobEntityName"></param>
        /// <param name="service"></param>
        public static void LogIntegrationError(IOrganizationService service, string title, string description, int errorCode, EntityReference relatedPayment)
        {
            Entity errorLog = new Entity(IntegrationError.ENTITY_NAME);
            errorLog[IntegrationError.TITLE] = title;
            errorLog[IntegrationError.DESCRIPTION] = description;
            errorLog[IntegrationError.CALL_TYPE] = new OptionSetValue(errorCode);
            errorLog[IntegrationError.RELATED_PAYMENT] = relatedPayment;
            service.Create(errorLog);
        }

        /// <summary>
        /// Returns the 
        /// </summary>
        /// <param name="clientKey"></param>
        /// <param name="clientId"></param>
        /// <param name="url"></param>
        /// <param name="endPoint"></param>
        /// <param name="jsonRequest"></param>
        /// <returns></returns>
        public static string GetAPIResponse(string clientKey, string clientId, string url, string endPoint, string jsonRequest)
        {
            string strResponse = string.Empty;
            HttpClient httpClient = new HttpClient();
            httpClient.DefaultRequestHeaders.Add("clientID", clientId);
            httpClient.DefaultRequestHeaders.Add("secret", clientKey);
            httpClient.DefaultRequestHeaders.Accept.Add(new MediaTypeWithQualityHeaderValue("application/json"));
            //httpClient.BaseAddress = new Uri(url);
            httpClient.Timeout = new TimeSpan(0, 2, 0);  // 2 minutes to execute within the sandbox mode

            HttpRequestMessage request = new HttpRequestMessage(HttpMethod.Post, url + endPoint) //TODO: Why POST? Why not GET?
            {
                Content = new StringContent(jsonRequest, Encoding.UTF8, "application/json")
            };

            HttpResponseMessage response = new HttpResponseMessage();
            try
            {
                response = httpClient.SendAsync(request).Result;
                strResponse = response.Content.ReadAsStringAsync().Result;

            }
            catch (Exception ex)
            {
                while (ex.InnerException != null) { ex = ex.InnerException; }

            }
            if (httpClient != null)
                httpClient.Dispose();
            return strResponse;
        }

        /// <summary>
        /// Bulk update entity records
        /// </summary>
        /// <param name="updatedPayments"></param>
        /// <returns></returns>
        public static ExecuteMultipleResponse BatchUpdateRecords(IOrganizationService service, List<Entity> entityList)
        {

            // Create an ExecuteMultipleRequest object.
            ExecuteMultipleRequest requestWithResults = new ExecuteMultipleRequest()
            {
                // Assign settings that define execution behavior: continue on error, return responses.
                Settings = new ExecuteMultipleSettings()
                {
                    ContinueOnError = false,
                    ReturnResponses = true
                },
                // Create an empty organization request collection.
                Requests = new OrganizationRequestCollection()
            };


            // Create several (local, in memory) entities in a collection.
            EntityCollection input = new EntityCollection(entityList);


            // Add a CreateRequest for each entity to the request collection.
            foreach (var entity in input.Entities)
            {
                UpdateRequest updateRequest = new UpdateRequest { Target = entity };
                requestWithResults.Requests.Add(updateRequest);
            }

            // Execute all the requests in the request collection using a single web method call.
            ExecuteMultipleResponse responseWithResults = (ExecuteMultipleResponse)service.Execute(requestWithResults);

            return responseWithResults;
        }

    }//End of Class

}
