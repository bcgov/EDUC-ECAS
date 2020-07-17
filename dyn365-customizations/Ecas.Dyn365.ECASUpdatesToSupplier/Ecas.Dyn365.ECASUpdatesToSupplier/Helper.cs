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
using Microsoft.Xrm.Sdk.Messages;
using Ecas.Dyn365.ECASUpdatesToSupplier.Model;
using Oracle.ManagedDataAccess.Client;
using System.Data;
using Microsoft.Crm.Sdk.Messages;

namespace Ecas.Dyn365.ECASUpdatesToSupplier
{
    public static class Helper
    {
        public static List<Entity> GetSystemConfigurations(IOrganizationService service, string group, string key)
        {
            List<Entity> result = new List<Entity>();

            QueryExpression exp = new QueryExpression(ConfigEntity.Schema.ENTITY_NAME);
            exp.NoLock = true;
            exp.ColumnSet.AllColumns = true;
            exp.Criteria.AddCondition(ConfigEntity.Schema.STATE_CODE, ConditionOperator.Equal, 0); //Active
            if (!string.IsNullOrEmpty(group))
                exp.Criteria.AddCondition(ConfigEntity.Schema.GROUP, ConditionOperator.Equal, group);
            if (!string.IsNullOrEmpty(key))
                exp.Criteria.AddCondition(ConfigEntity.Schema.KEY, ConditionOperator.Equal, key);

            var coll = service.RetrieveMultiple(exp);
            if (coll != null && coll.Entities != null && coll.Entities.Count > 0)
                result = coll.Entities.ToList();

            if (result.Count < 1)
                throw new InvalidPluginExecutionException(string.Format(Strings.CONFIGURATION_NOT_FOUND, group, key));

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

            if (result.Count < 1)
                throw new InvalidPluginExecutionException(string.Format(Strings.UNABLE_TO_FETCH_PAYMENT_RECORDS));

            return result;
        }

        /// <summary>
        /// 
        /// </summary>
        /// <param name="service"></param>
        /// <param name="tracing"></param>
        /// <returns></returns>
        public static List<Entity> GetAllContactsForCASUpdates(IOrganizationService service, ITracingService tracing)
        {
            List<Entity> result = new List<Entity>();

            QueryExpression exp = new QueryExpression(Contact.ENTITY_NAME);
            exp.NoLock = true;
            exp.ColumnSet.AllColumns = true;

            FilterExpression filter = new FilterExpression(LogicalOperator.Or);
            filter.AddCondition(Contact.SUPPLIER_STATUS, ConditionOperator.Equal, Contact.SUPPLIER_STATUSES.NEW_CAS_USER); // Fetch  "New CAS User"
            filter.AddCondition(Contact.SUPPLIER_STATUS, ConditionOperator.Equal, Contact.SUPPLIER_STATUSES.UPDATE_REQUESTED); // Fetch  "Update Requested"
            //exp.ColumnSet.AddColumns(new string[] { "educ_paymentid", "educ_assignment", "educ_invoicenumber" });
            exp.Criteria.AddCondition(Contact.STATE_CODE, ConditionOperator.Equal, 0); //Active
            exp.Criteria.AddFilter(filter);

            tracing.Trace("About to make call to retrieve contacts");

            try
            {
                var coll = service.RetrieveMultiple(exp);
                if (coll != null && coll.Entities != null && coll.Entities.Count > 0)
                    result = coll.Entities.ToList();
                //LogIntegrationError(service, "Testging GetAllContactsForCASUpdates ", "Result Counts = " + result.Count.ToString());
                tracing.Trace(string.Format("Fetched {0} Contact records", result.Count));

                if (result.Count < 1)
                {
                    tracing.Trace("Unable to Fetch Contact records");
                    throw new InvalidPluginExecutionException(string.Format("Unable to Fetch Contact records"));
                }
            }
            catch (Exception ex)
            {
                tracing.Trace("The error from the GetAllContactsForCASUpdates: " + ex.Message);
            }

            return result;
        }

        internal static string GetFormatedDescription(Entity contact, ErrorType errorType, IntegrationErrorCodes errorCode, string description)
        {
            StringBuilder builder = new StringBuilder();

            if (errorType == ErrorType.CONTACT_ERROR)
            {
                if (errorCode == IntegrationErrorCodes.FETCH_SUPPLIER)
                {
                    builder.Append(description).AppendLine("Contact ID = ").Append(contact.Id.ToString()).AppendLine("Contact Name = ")
                        .Append(contact[Contact.FULL_NAME]);
                }

            }

            return builder.ToString();

        }


        /// <summary>
        /// 
        /// </summary>
        /// <param name="contact"></param>
        /// <returns></returns>
        internal static OracleResponse GetContactDetailsFromT4A(Entity contact, string connString, ITracingService tracingService)
        {
            //TODO: Clean this
            //Create a connection to Oracle
            //string connString = "User Id=ECAS; password=blu3fob$;" +
            //"Data Source=oltp-scan01-dt.educ.gov.bc.ca:1521/oltpd.world; Pooling=false;";
            tracingService.Trace("Starting the GetContactDetailsFromT4A");

            OracleResponse oracleResponse = new OracleResponse();

            OracleConnection conn = new OracleConnection();
            conn.ConnectionString = connString;

            OracleCommand objCmd = new OracleCommand();
            objCmd.Connection = conn;
            objCmd.CommandText = ConfigEntity.ORACLE_COMMAND_TEXT; //Fetch from T4A the Supplier Number and SiteNumber
            objCmd.CommandType = CommandType.StoredProcedure;
            objCmd.Parameters.Add(ConfigEntity.StoredProcedureParams.PARTY_ID, OracleDbType.Double).Value = int.Parse(contact[Contact.PARTY_ID].ToString());
            objCmd.Parameters.Add(ConfigEntity.StoredProcedureParams.SUPPLIER_NO, OracleDbType.Varchar2, 100).Direction = ParameterDirection.Output;
            objCmd.Parameters.Add(ConfigEntity.StoredProcedureParams.SITE_NO, OracleDbType.Char, 3).Direction = ParameterDirection.Output;
            objCmd.Parameters.Add(ConfigEntity.StoredProcedureParams.STATUS_CODE, OracleDbType.Varchar2, 100).Direction = ParameterDirection.Output;
            objCmd.Parameters.Add(ConfigEntity.StoredProcedureParams.STATUS_T4A, OracleDbType.Varchar2, 100).Direction = ParameterDirection.Output;
            objCmd.Parameters.Add(ConfigEntity.StoredProcedureParams.TRANSACTION_MESSAGE, OracleDbType.Varchar2, 2000).Direction = ParameterDirection.Output;
            objCmd.Parameters.Add(ConfigEntity.StoredProcedureParams.TRANSACTION_CODE, OracleDbType.Varchar2, 100).Direction = ParameterDirection.Output;

            try
            {
                tracingService.Trace("Opening Connection");
                conn.Open();
                tracingService.Trace("Connection Opened");

                objCmd.ExecuteNonQuery();
                tracingService.Trace("Query Executed");

                oracleResponse.ContactId = contact.Id;
                oracleResponse.SupplierNumber = objCmd.Parameters[ConfigEntity.StoredProcedureParams.SUPPLIER_NO].Value.ToString();
                oracleResponse.SiteNumber = objCmd.Parameters[ConfigEntity.StoredProcedureParams.SITE_NO].Value.ToString();
                oracleResponse.StatusCode = objCmd.Parameters[ConfigEntity.StoredProcedureParams.STATUS_CODE].Value.ToString();
                oracleResponse.StatusT4A = objCmd.Parameters[ConfigEntity.StoredProcedureParams.STATUS_T4A].Value.ToString();
                oracleResponse.TranactionCode = objCmd.Parameters[ConfigEntity.StoredProcedureParams.TRANSACTION_CODE].Value.ToString();
                oracleResponse.TransactionMessage = objCmd.Parameters[ConfigEntity.StoredProcedureParams.TRANSACTION_MESSAGE].Value.ToString();

                tracingService.Trace("Populated Oracle Response");
            }
            catch (Exception ex)
            {
                throw new InvalidPluginExecutionException(OperationStatus.Failed, new StringBuilder().Append(Strings.STORED_PROCEDURE_EXCEPTION)
                    .Append(ConfigEntity.ORACLE_COMMAND_TEXT).AppendLine(" Exception:\n").AppendLine(ex.Message).ToString());
            }
            finally
            {
                tracingService.Trace("Closing connection");
                conn.Close();
                tracingService.Trace("Connection closed");
            }

            return oracleResponse;
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
        public static void CreateCronJobSingletonRecord(IOrganizationService service, string entityName, string namefield, string nameValue)
        {
            Entity newCronJob = new Entity(entityName);
            newCronJob[namefield] = nameValue;
            service.Create(newCronJob);
        }

        /// <summary>
        /// Creates a new record for the Cron Job Entity in Dynamics to enable next trigger of the plugin when the record is deleted by the Bulk
        /// deletion job.
        /// </summary>
        /// <param name="cronJobEntityName"></param>
        /// <param name="service"></param>
        public static void LogIntegrationError(IOrganizationService service, string title, string description, int errorCode, EntityReference relatedRecord)
        {
            Entity errorLog = new Entity(IntegrationError.ENTITY_NAME);
            errorLog[IntegrationError.TITLE] = title;
            errorLog[IntegrationError.DESCRIPTION] = description;
            errorLog[IntegrationError.CALL_TYPE] = new OptionSetValue(errorCode);
            errorLog[IntegrationError.RELATED_CONTACT] = relatedRecord;
            service.Create(errorLog);
        }

        /// <summary>
        /// Creates a new record for the Cron Job Entity in Dynamics to enable next trigger of the plugin when the record is deleted by the Bulk
        /// deletion job.
        /// </summary>
        /// <param name="cronJobEntityName"></param>
        /// <param name="service"></param>
        public static void LogIntegrationError(IOrganizationService service, string title, string description)
        {
            Entity errorLog = new Entity(IntegrationError.ENTITY_NAME);
            errorLog[IntegrationError.TITLE] = title;
            errorLog[IntegrationError.DESCRIPTION] = description;
            service.Create(errorLog);
        }


        /// <summary>
        /// Creates a new record for the Cron Job Entity in Dynamics to enable next trigger of the plugin when the record is deleted by the Bulk
        /// deletion job.
        /// </summary>
        /// <param name="cronJobEntityName"></param>
        /// <param name="service"></param>
        public static void LogIntegrationError(IOrganizationService service, ErrorType type, int errorCode, string description, string title, EntityReference relatedRecord)
        {

            Entity errorLog = new Entity(IntegrationError.ENTITY_NAME);
            errorLog[IntegrationError.TITLE] = title;
            errorLog[IntegrationError.DESCRIPTION] = description;
            errorLog[IntegrationError.CALL_TYPE] = new OptionSetValue(errorCode);

            if (type == ErrorType.PAYMENT_ERROR)
                errorLog[IntegrationError.RELATED_PAYMENT] = relatedRecord;

            else if (type == ErrorType.CONTACT_ERROR)
                errorLog[IntegrationError.RELATED_CONTACT] = relatedRecord;

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

        /// <summary>
        /// Set the Status of the Entity Record based on the passed parameters
        /// </summary>
        /// <param name="service">Organization Service</param>
        /// <param name="target">Entity Reference</param>
        /// <param name="stateCode">Status</param>
        /// <param name="statusCode">Status Reason</param>
        internal static void SetState(IOrganizationService service, EntityReference target, int stateCode, int statusCode)
        {
            SetStateRequest req = new SetStateRequest
            {
                EntityMoniker = target,
                State = new OptionSetValue(stateCode),
                Status = new OptionSetValue(statusCode)
            };

            service.Execute(req);
        }


    }//End of Class


}