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
    public class VerifyAndUpdateCasPaymentStatus : WorkFlowActivityBase 
    {
        [RequiredArgument]
        [Input("Payment")]
        //[ReferenceTarget("educ_payment")]
        public InArgument<string> Payment   { get; set; }

        [Input("Success")]
        public InOutArgument<bool> Success { get; set; }

        [Input("ErrorMessage")]
        public InOutArgument<string> ErrorMessage { get; set; }

        public override void ExecuteCRMWorkFlowActivity(CodeActivityContext context, LocalWorkflowContext crmWorkflowContext)
        {
            //Read Payment Id
            var paymentIdString = Payment.Get<string>(context);

            Guid paymentId = Guid.Empty;
            Guid.TryParse(paymentIdString, out paymentId);

            if (paymentId == Guid.Empty) throw new InvalidPluginExecutionException("Invalid Payment Id. Id must be a Guid");

            crmWorkflowContext.TracingService.Trace("Payment Id retrieved");
            Utils.Payment paymentUtils = new Utils.Payment(crmWorkflowContext.OrganizationService,
                crmWorkflowContext.TracingService, paymentId);
            var result = paymentUtils.VerifyAndUpdateStatus();

            Success.Set(context, result.Success);
            ErrorMessage.Set(context, result.Message);

        }
    }
}
