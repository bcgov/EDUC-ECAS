using Ecas.Dyn365.CASIntegration.Activities;
using Microsoft.Xrm.Sdk;
using Microsoft.Xrm.Sdk.Workflow;
using System;
using System.Activities;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace Ecas.Dyn365.CASIntegration.Workflows
{
    public class VerifyCasPaymentStatus : WorkFlowActivityBase 
    {
        [RequiredArgument]
        [Input("Payment")]
        [ReferenceTarget("educ_payment")]
        public InArgument<EntityReference> Payment   { get; set; }

        [Output("Success")]
        public OutArgument<bool> Success { get; set; }

        [Output("ErrorMessage")]
        public OutArgument<string> ErrorMessage { get; set; }

        public override void ExecuteCRMWorkFlowActivity(CodeActivityContext context, LocalWorkflowContext crmWorkflowContext)
        {
            //Read Payment Id
            var paymentId = Payment.Get<EntityReference>(context).Id;
            crmWorkflowContext.TracingService.Trace("Payment Id retrieved");
            Utils.Payment paymentUtils = new Utils.Payment(paymentId, crmWorkflowContext.OrganizationService,
                crmWorkflowContext.TracingService);
            paymentUtils.VerifyStatus();
        }
    }
}
