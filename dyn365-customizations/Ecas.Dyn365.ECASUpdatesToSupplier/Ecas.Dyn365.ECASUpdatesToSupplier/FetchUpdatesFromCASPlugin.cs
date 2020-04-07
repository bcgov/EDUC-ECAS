using Microsoft.Xrm.Sdk;
using System;
using System.ServiceModel;
using System.Collections.Generic;
using Microsoft.Xrm.Sdk.Messages;
using Ecas.Dyn365.ECASUpdatesToSupplier.Model;

namespace Ecas.Dyn365.ECASUpdatesToSupplier
{
    public class FetchUpdatesFromCASPlugin : IPlugin
    {

        public void Execute(IServiceProvider serviceProvider)
        {
            // Obtain the tracing service
            ITracingService tracingService = (ITracingService)serviceProvider.GetService(typeof(ITracingService));

            // Obtain the execution context from the service provider.  
            IPluginExecutionContext context = (IPluginExecutionContext)serviceProvider.GetService(typeof(IPluginExecutionContext));

            // The InputParameters collection contains all the data passed in the message request.  
            if (context.InputParameters.Contains(Strings.TARGET) && context.InputParameters != null)
            {
                // Obtain the target entity from the input parameters.  
                // Obtain the organization service reference which you will need for  
                // web service calls.  
                IOrganizationServiceFactory serviceFactory =
                    (IOrganizationServiceFactory)serviceProvider.GetService(typeof(IOrganizationServiceFactory));
                IOrganizationService service = serviceFactory.CreateOrganizationService(context.UserId);

                tracingService.Trace(Strings.LOADED_UPDATE_SUPPLIER_PLUGIN);
                tracingService.Trace(Strings.PLUGIN_DEPTH + context.Depth.ToString());

                if (context.Depth > 2)
                {
                    return;
                }

             
                //Fetch all contact records that have Supplier Status (OptionSet) = "New CAS User" OR "Update Requested"          
                List<Entity> contactsToFetch = Helper.GetAllContactsForCASUpdates(service, tracingService);
                tracingService.Trace("Fetched the contacts");
                List<Entity> updatedContacts = new List<Entity>();
                Entity contactItem;

                tracingService.Trace("Fetching the Configs");
                ////Get the configuration record for Oracle_T4A group from the configs entity and get the connection value from the record.
                var configs = Helper.GetSystemConfigurations(service, ConfigEntity.Group.ORACLE_T4A, string.Empty);
                tracingService.Trace("Fetching Connection");
                string connection = Helper.GetConfigKeyValue(configs, ConfigEntity.Key.CONNECTION, ConfigEntity.Group.ORACLE_T4A);

                tracingService.Trace("Fetched Configs and Connection");

                int partyId;
                string supplierNumber = string.Empty;
                string siteNumber = string.Empty;
                string statusCode = string.Empty;
                string statusT4A = string.Empty;
                string transactionMessage = string.Empty;

                //Iterate over the contact records to call the stored procedure and get back results from T4A SP
                foreach (Entity contact in contactsToFetch)
                {
                    if (contact.Contains(Contact.ID))
                    {
                        tracingService.Trace(string.Format("Contact ID = {0}", contact.Id));
                    }


                    if (!contact.Contains(Contact.PARTY_ID))
                    {
                        tracingService.Trace("Inside Party ID doesnt exists");
                        //Create the Error Log that party ID doesn't exist. 
                        Helper.LogIntegrationError(service, Strings.PARTY_ID_MISSING,
                            Helper.GetFormatedDescription(contact, ErrorType.CONTACT_ERROR, IntegrationErrorCodes.FETCH_SUPPLIER, Strings.PARTY_ID_MISSING_DESCRIPTION),
                                IntegrationErrorCodes.FETCH_SUPPLIER.GetIntValue(), new EntityReference(Contact.ENTITY_NAME, contact.Id));

                        tracingService.Trace("CheckPoint 100");
                        if (contact.Attributes.Contains(Contact.SUPPLIER_STATUS))
                        {
                            tracingService.Trace("CheckPoint 200");
                            //Update the Contact Record with the T4A Error Occurred
                            contact[Contact.SUPPLIER_STATUS] = new OptionSetValue(Contact.SUPPLIER_STATUSES.T4A_ERROR_OCCURRED);
                            service.Update(contact);
                        }
                        tracingService.Trace("CheckPoint 300");
                    }
                    else
                    {
                        tracingService.Trace("Since Party ID exists, getUpdatedContact");
                        partyId = int.Parse(contact[Contact.PARTY_ID].ToString());
                        tracingService.Trace("PartyID = " + partyId.ToString());

                        OracleResponse response = Helper.GetContactDetailsFromT4A(contact, connection, tracingService);
                        tracingService.Trace("Got Oracle Response");


                        contactItem = GetUpdatedContact(response, service, tracingService);
                        tracingService.Trace("Got Updated ContactItem");

                        //If the contact returned is not null, add it to the update job
                        if (contactItem != null)
                        {
                            tracingService.Trace("adding contact item to the updatedContacts list");
                            updatedContacts.Add(contactItem);
                        }

                    }

                }

                try
                {
                    tracingService.Trace("Sending the updatedContacts to Helper.BatchUpdateRecordsMethod");
                    //Update the contact records 
                    ExecuteMultipleResponse responses = Helper.BatchUpdateRecords(service, updatedContacts);
                    tracingService.Trace("Got the responses back from Helper.BatchUpdateRecords");
                    //    //Create the cron job singleton record
                    Helper.CreateCronJobSingletonRecord(service, SupplierCronJob.ENTITY_NAME, SupplierCronJob.NAME, Strings.SINGLETON_RECORD);

                }

                catch (FaultException<OrganizationServiceFault> ex)
                {
                    tracingService.Trace(Strings.UPDATE_SUPPLIER_PLUGIN_SHORT_ERROR, ex.ToString());
                    throw new InvalidPluginExecutionException(Strings.UPDATE_SUPPLIER_PLUGIN_ERROR_OCCURED, ex);
                }

                catch (Exception ex)
                {
                    tracingService.Trace(Strings.UPDATE_SUPPLIER_PLUGIN_SHORT_ERROR, ex.ToString());
                    throw;
                }
            }//End of IF condition
        }//End of Execute



        /// <summary>
        /// 
        /// </summary>
        /// <param name="v"></param>
        /// <returns></returns>
        private Entity GetUpdatedContact(OracleResponse oracleResponse, IOrganizationService service, ITracingService tracingService)
        {
            Entity contact = new Entity(Contact.ENTITY_NAME);

            tracingService.Trace(string.Format("Hi! ORACLEResponse.StatusT4A = {0}", oracleResponse.StatusT4A));

            if (oracleResponse.StatusT4A == OracleResponse.T4A_STATUS.OK)
            {

                tracingService.Trace(string.Format("oracleResponse.ContactId = {0}", oracleResponse.ContactId));
                tracingService.Trace(string.Format("oracleResponse.SiteNumber = {0}", oracleResponse.SupplierNumber));
                tracingService.Trace(string.Format("oracleResponse.SiteNumber = {0}", oracleResponse.SiteNumber));

                contact[Contact.ID] = oracleResponse.ContactId;
                contact[Contact.SUPPLIER_NUMBER] = oracleResponse.SupplierNumber;
                contact[Contact.SUPPLIER_SITE_NUMBER] = oracleResponse.SiteNumber;
                contact[Contact.SUPPLIER_STATUS] = new OptionSetValue( Contact.SUPPLIER_STATUSES.SUPPLIER_VERIFIED);
                tracingService.Trace("Contact populated");
                return contact;
            }
            else
            {
                //TODO: 
                //Log Error. Handle the bad request here
                tracingService.Trace("Going to Log the Integration error now");
                //Helper.LogIntegrationError(service, Strings.T4A_FER_ERROR, Helper.GetFormatedDescription(contact, ErrorType.CONTACT_ERROR, IntegrationErrorCodes.FETCH_SUPPLIER, Strings.PARTY_ID_MISSING_DESCRIPTION),
                //               IntegrationErrorCodes.FETCH_SUPPLIER.GetIntValue(), new EntityReference(Contact.ENTITY_NAME, contact.Id));

                Helper.LogIntegrationError(service, "T4A returned FER", "FER Description", IntegrationErrorCodes.FETCH_SUPPLIER.GetIntValue(), new EntityReference(Contact.ENTITY_NAME, oracleResponse.ContactId));
                contact[Contact.ID] = oracleResponse.ContactId;
                contact[Contact.SUPPLIER_STATUS] = new OptionSetValue(Contact.SUPPLIER_STATUSES.T4A_ERROR_OCCURRED);
                service.Update(contact);
         
                return null;

            }

        }

    }
}
