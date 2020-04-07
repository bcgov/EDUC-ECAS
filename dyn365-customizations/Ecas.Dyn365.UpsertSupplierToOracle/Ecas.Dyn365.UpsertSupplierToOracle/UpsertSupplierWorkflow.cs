using Ecas.Dyn365.Model;
using Microsoft.Crm.Sdk.Messages;
using Microsoft.Xrm.Sdk;
using Microsoft.Xrm.Sdk.Messages;
using Microsoft.Xrm.Sdk.Query;
using Microsoft.Xrm.Sdk.Workflow;
using System;
using System.Activities;
using System.Collections.ObjectModel;

namespace Ecas.Dyn365.UpsertSupplierToOracle
{


    public class UpsertSupplierWorkflow : CodeActivity
    {
        [RequiredArgument]
        [ReferenceTarget("contact")]
        [Input("Contact Reference")]
        public InArgument<EntityReference> ContactReference { get; set; }

        #region Input parameters
        [RequiredArgument]
        [Input("First Name")]
        [Default("")]
        public InArgument<string> FirstName { get; set; }

        [RequiredArgument]
        [Input("Last Name")]
        [Default("")]
        public InArgument<string> LastName { get; set; }

        [RequiredArgument]
        [Input("Payment Method")]
        [Default("CHQ")]
        public InArgument<string> PaymentMethod { get; set; }

        [RequiredArgument]
        [Input("SIN")]
        [Default("")]
        public InArgument<string> SIN { get; set; }

        [RequiredArgument]
        [Input("Address 1")]
        [Default("")]
        public InArgument<string> Address1 { get; set; }

        [RequiredArgument]
        [Input("City")]
        [Default("")]
        public InArgument<string> City { get; set; }

        [RequiredArgument]
        [Input("Postal Code")]
        [Default("")]
        public InArgument<string> PostalCode { get; set; }

        [RequiredArgument]
        [Input("Province")]
        public InArgument<string> Province { get; set; }

        [RequiredArgument]
        [Input("Country")]
        [Default("")]
        public InArgument<string> Country { get; set; }
        #endregion

        #region Output parameters
        //Output Parameters

        [Default("-1")]
        [Input("Party ID")]
        [Output("Out Party ID")]
        public InOutArgument<int> PartyID { get; set; }

        [Default("Custom")]
        [Output("Transaction Code")]
        public OutArgument<string> TransactionCode { get; set; }

        [Default("Custom")]
        [Output("Transaction Messsage")]
        public OutArgument<string> TransactionMessage { get; set; }
        #endregion

        protected override void Execute(CodeActivityContext executionContext)
        {
            //Create the tracing service
            ITracingService tracingService = executionContext.GetExtension<ITracingService>();

            //Create the context
            IWorkflowContext context = executionContext.GetExtension<IWorkflowContext>();
            IOrganizationServiceFactory serviceFactory = executionContext.GetExtension<IOrganizationServiceFactory>();
            IOrganizationService service = serviceFactory.CreateOrganizationService(context.UserId);

            tracingService.Trace("Loaded UpsertSupplierWorkflow");
            tracingService.Trace("CREATING Contact object with the input values");
            tracingService.Trace("Checkpoint 1");

            Contact contact = new Contact();

            int partyid = PartyID.Get<int>(executionContext);


            tracingService.Trace("Starting assigning attribute variables.");

            contact.First_Name = FirstName.Get<string>(executionContext).ToString();
            tracingService.Trace("First Name Populated");

            tracingService.Trace(string.Format("Setting party id = {0}", partyid));
            contact.Party_Id = partyid;
            tracingService.Trace("Party ID populated");

            contact.Last_Name = LastName.Get<string>(executionContext).ToString();
            tracingService.Trace("Last Name Populated");

            contact.Payment_Method = PaymentMethod.Get<string>(executionContext).ToString();
            tracingService.Trace("Payment Method Populated");

            contact.SIN = SIN.Get<string>(executionContext).ToString();
            tracingService.Trace("SIN Populated");

            contact.Address1 = Address1.Get<string>(executionContext).ToString();
            tracingService.Trace("Address1 Populated");

            contact.Country_Code = Country.Get<string>(executionContext).ToString();
            tracingService.Trace("Country Populated");

            contact.City = City.Get<string>(executionContext).ToString();
            tracingService.Trace("City Populated");

            contact.Province_Code = Province.Get<string>(executionContext).ToString();
            tracingService.Trace("Province Populated");

            contact.Postal_Code = PostalCode.Get<string>(executionContext).ToString();
            tracingService.Trace("Postal Code Populated");
            tracingService.Trace("Checkpoint 2");


            EntityReference contactRef = ContactReference.Get<EntityReference>(executionContext);
            contact.ID = contactRef.Id;

            tracingService.Trace("Fetching the Configs");
            ////Get the configuration record for Oracle_T4A group from the configs entity and get the connection value from the record.
            var configs = Helper.GetSystemConfigurations(service, ConfigEntity.Group.ORACLE_T4A, string.Empty);
            tracingService.Trace("Fetching Connection");
            string connection = Helper.GetConfigKeyValue(configs, ConfigEntity.Key.CONNECTION, ConfigEntity.Group.ORACLE_T4A);

            try
            {
                tracingService.Trace("Fetching Oracle Response by making call to Helper.UpsertSupplierInOracle()");
                OracleResponse response = Helper.UpsertSupplierInOracle(contact, connection, tracingService);

                if (response.TransactionCode == OracleResponse.T4A_STATUS.OK)
                {
                    tracingService.Trace("Inside OracleResponse.T4A_STATUS.OK \nRetriving and setting response values to output");
                    PartyID.Set(executionContext, response.PartyId);
                    tracingService.Trace("Party ID Output Param is set");
                }

                tracingService.Trace("Setting up transaction code and transaction message");
                TransactionCode.Set(executionContext, response.TransactionCode.Trim());
                TransactionMessage.Set(executionContext, response.TransactionMessage);

            }
            catch (InvalidWorkflowException ex)
            {
                Helper.LogIntegrationError(service, ErrorType.CONTACT_ERROR, IntegrationErrorCodes.UPSERT_SUPPLIER.GetIntValue(), Strings.T4A_FER_ERROR,
                    string.Format("Error Description: {0} ", ex.Message), contactRef);
            }
            catch (Exception ex)
            {
                Helper.LogIntegrationError(service, ErrorType.CONTACT_ERROR, IntegrationErrorCodes.UPSERT_SUPPLIER.GetIntValue(), Strings.T4A_FER_ERROR,
                 string.Format("Error Description: {0} ", ex.Message), contactRef);
            }

        }

    }
}

