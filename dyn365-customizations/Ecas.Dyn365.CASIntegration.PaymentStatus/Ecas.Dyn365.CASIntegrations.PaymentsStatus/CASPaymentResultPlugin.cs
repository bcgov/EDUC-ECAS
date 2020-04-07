using Ecas.Dyn365.CASIntegration.Plugin;
using Ecas.Dyn365.CASIntegrations.PaymentsStatus.Models;
using Ecas.Dyn365.CASIntegrations.PaymentsStatus.StringConstants;
using Microsoft.Crm.Sdk.Messages;
using Microsoft.Xrm.Sdk;
using Microsoft.Xrm.Sdk.Query;
using Newtonsoft.Json;
using System;
using System.Collections.Generic;
using System.Text;

namespace Ecas.Dyn365.CASIntegrations.PaymentsStatus
{
    public class CASPaymentResultPlugin : IPlugin
    {
        public void Execute(IServiceProvider serviceProvider)
        {
            IPluginExecutionContext context = (IPluginExecutionContext)serviceProvider.GetService(typeof(IPluginExecutionContext));
            IOrganizationServiceFactory serviceFactory = (IOrganizationServiceFactory)serviceProvider.GetService(typeof(IOrganizationServiceFactory));
            IOrganizationService service = serviceFactory.CreateOrganizationService(context.UserId);
            ITracingService traceService = (ITracingService)serviceProvider.GetService(typeof(ITracingService));

            traceService.Trace(Strings.LOADED_CAS_PAYMENT_RESULTS_PLUGIN);
            traceService.Trace(Strings.PLUGIN_DEPTH + context.Depth.ToString());

            if (context.Depth > 2)
            {
                return;
            }

            traceService.Trace(Strings.LOADED_TARGET_ENTITY);

               //Fetch configurations from Configuration Entity in Dynamics to prepare for HTTP API Call
            var configs = Helper.GetSystemConfigurations(service, ConfigConstants.CAS_AP, string.Empty);
            string clientKey = Helper.GetConfigKeyValue(configs, ConfigConstants.CLIENT_KEY, ConfigConstants.CAS_AP);
            string clientId = Helper.GetConfigKeyValue(configs, ConfigConstants.CLIENT_ID, ConfigConstants.CAS_AP);
            string url = Helper.GetConfigKeyValue(configs, ConfigConstants.INTERFACE_URL, ConfigConstants.CAS_AP);
            string endPoint = Helper.GetConfigKeyValue(configs, ConfigConstants.ENDPOINT, ConfigConstants.CAS_AP);


            // Get the Pending Payment records with the status "Sent To CAS" 
            List<Entity> pendingPayments = Helper.GetPaymentRecordsForProcessing(service);

                //Get the response for each payment in the fetched payment records
            foreach (Entity payment in pendingPayments)
            {
                string invoiceNumber = String.Empty;

                if (payment.Attributes.Keys.Contains(Payment.INVOICE_NUMBER))
                {
                    invoiceNumber = payment[Payment.INVOICE_NUMBER].ToString();

                    EntityReference assignment = (EntityReference)payment[Payment.Assignment.ENTITY_NAME]; //Get the EntityReference of the related assignment of the payment record

                    //Fetch the Related Assignment Entity fields from Dynamics
                    Entity relatedAssignment = service.Retrieve(Payment.Assignment.ENTITY_NAME, assignment.Id, new ColumnSet(new string[] { Payment.Assignment.RELATED_CONTACT }));

                    EntityReference contactReference = (EntityReference)relatedAssignment[Payment.Assignment.RELATED_CONTACT];//Fetch the related contact entity reference from the assignment record

                    //Fetch the Related Contact Entity fields from Dynamics 
                    Entity relatedContact = service.Retrieve(Contact.ENTITY_NAME, contactReference.Id, new ColumnSet(new string[] { Contact.SITE_NUMBER, Contact.SUPPLIER_NUMBER }));

                    //Prepare Invoice object for Serialization 
                    Invoice invoice = new Invoice
                    {
                        InvoiceNumber = invoiceNumber,
                        SupplierNumber = relatedContact[Contact.SUPPLIER_NUMBER].ToString(),
                        SupplierSiteNumber = relatedContact[Contact.SITE_NUMBER].ToString(),
                        PaymentID = payment.Id
                    };

                    //Get Json from Invoice Object
                    string jsonRequest = JsonConvert.SerializeObject(invoice);

                    //Call the API and deserialize the response to PaymentResponse object 
                    PaymentResponse response = JsonConvert.DeserializeObject<PaymentResponse>(Helper.GetAPIResponse(clientKey, clientId, url, endPoint, jsonRequest));

                    if (response.invoice_status == CASResponseStatus.InvoiceStatus.VALIDATED)
                    {

                        Entity updatedPayment = new Entity(Payment.ENTITY_NAME);
                        updatedPayment[Payment.INVOICE_STATUS] = response.invoice_status;
                        updatedPayment[Payment.PAYMENT_STATUS] = response.payment_status;
                        updatedPayment[Payment.PAYMENT_DATE] = DateTime.Parse(response.payment_date);
                        updatedPayment[Payment.PAYMENT_NUMBER] = response.payment_number;
                        updatedPayment[Payment.PAYMENT_ID] = payment.Id;

                        service.Update(updatedPayment);
                        SetState(service, new EntityReference(Payment.ENTITY_NAME, payment.Id), Payment.InActiveStatus.STATE_CODE, Payment.InActiveStatus.PAYMENT_PROCESSED_STATUS_REASON);

                        //Update Expenses for this payment to PAID
                        //Get related expenses
                        List<Entity> expenses = Helper.GetRelatedChildRecords(service, payment.Id, Expense.RELATED_PAYMENT, Expense.ENTITY_NAME, false);

                        if (expenses.Count > 0)
                        {
                            //Update the status to Inactive -- Paid for all related expenses
                            foreach (Entity expense in expenses)
                            {
                                SetState(service, new EntityReference(Expense.ENTITY_NAME, expense.Id), Expense.InActiveStatus.STATE_CODE, Expense.InActiveStatus.PAID_STATUS_REASON);

                            }
                        }
                        else
                        {
                            //Unhandled case as Payment is not supposed to have Sent To CAS status without a expense associted

                        }

                    }
                    else if (response.invoice_status == CASResponseStatus.InvoiceStatus.NOT_VALIDATED)
                    {
                        //Create the log when the INVOICE STATUS is not found in CAS
                        Helper.LogIntegrationError(service, Strings.NOT_PAID, response.ToString(Strings.INVOICE_DESC_NOT_PAID), 
                            IntegrationErrorCodes.GetIntValueFromCode(IntegrationErrorCodes.FETCH_PAYMENT_RESULTS_FROM_CAS), new EntityReference(Payment.ENTITY_NAME, payment.Id));
                    }

                    else //Handle all other errors
                    {
                        if (response.invoice_status == CASResponseStatus.InvoiceStatus.NOT_FOUND)
                        {
                            //Create the log when the INVOICE STATUS is not found in CAS
                            Helper.LogIntegrationError(service, Strings.INVOICE_STATUS_NOT_FOUND, response.ToString(Strings.INVOICE_NOT_FOUND_DESC), 
                                IntegrationErrorCodes.GetIntValueFromCode(IntegrationErrorCodes.FETCH_PAYMENT_RESULTS_FROM_CAS), new EntityReference(Payment.ENTITY_NAME, payment.Id));
                        }
                        else
                        {
                        
                            //Everything else
                            Helper.LogIntegrationError(service, Strings.UNKNOWN_INVOICE_ERROR, response.ToString(Strings.UNKNOWN_INVOICE_ERROR), 
                                IntegrationErrorCodes.GetIntValueFromCode(IntegrationErrorCodes.FETCH_PAYMENT_RESULTS_FROM_CAS), new EntityReference(Payment.ENTITY_NAME, payment.Id));
                        }

                        //set the status of the payment to Failed
                        SetState(service, new EntityReference(Payment.ENTITY_NAME, payment.Id), Payment.ActiveStatus.STATE_CODE, 
                            Payment.ActiveStatus.CAS_PROCESSING_ERROR_STATUS_REASON);
                    }

                }
                else //if no invoice number on the payment
                {
                    Helper.LogIntegrationError(service, Strings.INOVICE_NOT_FOUND_TITLE, Strings.INVOICE_NOT_FOUND_DESC ,IntegrationErrorCodes.GetIntValueFromCode(IntegrationErrorCodes.FETCH_PAYMENT_RESULTS_FROM_CAS),
               new EntityReference(Payment.ENTITY_NAME, payment.Id));

                    SetState(service, new EntityReference(Payment.ENTITY_NAME, payment.Id), Payment.ActiveStatus.STATE_CODE, Payment.ActiveStatus.FAILED_STATUS_REASON);

                    traceService.Trace(Strings.INOVICE_NOT_FOUND_TITLE + "-" + payment[Payment.PAYMENT_NAME]);
                }

            } // End of For-each loop for payments.

            traceService.Trace(Strings.FINISHED_PAYMENTS_FETCH_PROCESSING);
            //Since the singleton record is deleted, create new CAS AP Cron Job Proxy Singleton record 
            Helper.CreateCronJobSingletonRecord(Payment.CAS_AP_CRON_JOB_PROXY.ENTITY_NAME, service);

            traceService.Trace(Strings.CREATED_SINGLETON_CAS_AP_CRON_JOB);
        }


        /// <summary>
        /// Set the Status of the Entity Record based on the passed parameters
        /// </summary>
        /// <param name="service">Organization Service</param>
        /// <param name="target">Entity Reference</param>
        /// <param name="stateCode">Status</param>
        /// <param name="statusCode">Status Reason</param>
        private void SetState(IOrganizationService service, EntityReference target, int stateCode, int statusCode)
        {
            SetStateRequest req = new SetStateRequest
            {
                EntityMoniker = target,
                State = new OptionSetValue(stateCode),
                Status = new OptionSetValue(statusCode)
            };

            service.Execute(req);
        }


    }//End of CASPaymentResultPlugin

}// End of Namespace

