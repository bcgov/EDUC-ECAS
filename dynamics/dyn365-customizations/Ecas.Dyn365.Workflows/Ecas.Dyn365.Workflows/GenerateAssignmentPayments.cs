using Microsoft.Xrm.Sdk;
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
            assignmentUtils.GeneratedPaymentRecords();
        }
    }
}
