using Microsoft.Xrm.Sdk;
using Microsoft.Xrm.Sdk.Query;
using Microsoft.Xrm.Sdk.Workflow;
using System;
using System.Activities;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace Ecas.Dyn365.Workflows
{
    public class GenerateAssignmentPayments : WorkFlowActivityBase
    {
        [RequiredArgument]
        [Input("Assignment")]
        [ReferenceTarget("educ_assignment")]
        public InArgument<EntityReference> Assignment { get; set; }

        public override void ExecuteCRMWorkFlowActivity(CodeActivityContext context, LocalWorkflowContext crmWorkflowContext)
        {
            //Read Assignment Id
            var assignmentId = Assignment.Get<EntityReference>(context).Id;
            crmWorkflowContext.TracingService.Trace("Assignment Id retrieved");
            Utils.Assigment assignmentUtils = new Utils.Assigment(assignmentId, crmWorkflowContext.OrganizationService,
                crmWorkflowContext.TracingService);

            Entity assignment = crmWorkflowContext.OrganizationService.Retrieve("educ_assignment", assignmentId, new ColumnSet(new string[] { "educ_contact" }));

            EntityReference contactReference = (EntityReference)assignment["educ_contact"];

            //Fetch the Related Contact Entity fields from Dynamics 
            Entity relatedContact = crmWorkflowContext.OrganizationService.Retrieve("contact", contactReference.Id, new ColumnSet(new string[] { "educ_supplierissue" }));

            string contactSupplierStatus = relatedContact.FormattedValues["educ_supplierissue"];

            //LogSupplierStatusValue(crmWorkflowContext, contactSupplierStatus);

            if (contactSupplierStatus == "Supplier Verified")
            {
                assignmentUtils.GeneratedPaymentRecords();
            }
            else
            {
                crmWorkflowContext.Trace(string.Format("\n Custom Message from GenerateAssignmentPayment Workflow: \n \"The Contact Supplier Status  = {0}\" \n End of Message: \n\n", contactSupplierStatus));
                throw new InvalidPluginExecutionException(OperationStatus.Canceled);
            }


        }

        /// <summary>
        /// 
        /// </summary>
        /// <param name="crmWorkflowContext"></param>
        /// <param name="contactSupplierStatus"></param>
        private static void LogSupplierStatusValue(LocalWorkflowContext crmWorkflowContext, string contactSupplierStatus)
        {
            Entity integrationLog = new Entity("educ_integrationerrorlogs");
            integrationLog["educ_name"] = "Testing 007";
            integrationLog["educ_description"] = string.Format("Contact Supplier Status = {0}", contactSupplierStatus);
            crmWorkflowContext.OrganizationService.Create(integrationLog);
        }
    }
}
