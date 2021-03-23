using Microsoft.Crm.Sdk.Messages;
using Microsoft.Xrm.Sdk;
using Microsoft.Xrm.Sdk.Query;
using Microsoft.Xrm.Sdk.Workflow;
using System;
using System.Activities;


namespace Ecas.Dyn365.Workflows
{
    public class UpdateRelatedAssignments : WorkFlowActivityBase
    {
        [Input("Session")]
        [ReferenceTarget("educ_session")]
        public InArgument<EntityReference> Session { get; set; }

        public enum InactiveAssignmentStatus
        {
            Declined 	= 2,
            Withdrew	= 610410004,
            Applied		= 610410010,
            Selected	= 610410011,
            Invited		= 610410012,
            Accepted	= 610410013,
            Contract	= 610410014,
            Confirmed	= 610410015,
            AttendanceRecorded = 610410016
        }

        public enum ActiveAssignmentStatus
        {
            Declined = 610410008,
            Applied = 1,
            Selected = 610410006,
            Invited = 610410000,
            Accepted = 610410001,
            Contract = 610410002,
            Confirmed = 610410003,
            AttendanceRecorded = 610410007
        }
        public enum State
        {
            Active = 0,
            Inactive = 1
        }
       
        public enum SessionStatus
        {
            Cancelled = 610410005,
            Completed = 2
        }
 
        public override void ExecuteCRMWorkFlowActivity(CodeActivityContext context, LocalWorkflowContext crmWorkflowContext)
        {
            EntityCollection assignments = new EntityCollection();
            EntityReference session = Session.Get(context);
            Guid sessionId = session.Id;

            int StatusReason;
            InactiveAssignmentStatus setInactiveStatus;
            int currentAssignmentStatus;

            crmWorkflowContext.TracingService.Trace($"SessionId: {sessionId}");
 
            var QEeduc_session = new QueryExpression("educ_session");
            QEeduc_session.ColumnSet.AddColumns("statuscode");
            QEeduc_session.Criteria.AddCondition("educ_sessionid", ConditionOperator.Equal, sessionId);
            var QEeduc_session_educ_assignment = QEeduc_session.AddLink("educ_assignment", "educ_sessionid", "educ_session");
            QEeduc_session_educ_assignment.Columns.AddColumns("educ_name", "statuscode", "statecode", "educ_assignmentid");
            assignments = crmWorkflowContext.OrganizationService.RetrieveMultiple(QEeduc_session);

            var stateCode = new OptionSetValue();
            var statusCode = new OptionSetValue();

            foreach (var assignment in assignments.Entities)
            {
                StatusReason = (int)assignment.GetAttributeValue<OptionSetValue>("statuscode").Value;

                if (StatusReason == (int)SessionStatus.Completed || StatusReason == (int)SessionStatus.Cancelled)
                {
                    currentAssignmentStatus = ((OptionSetValue)assignment.GetAttributeValue<AliasedValue>("educ_assignment1.statuscode").Value).Value;

                    switch ((ActiveAssignmentStatus)currentAssignmentStatus)
                    {
                        case ActiveAssignmentStatus.Accepted:
                            setInactiveStatus = InactiveAssignmentStatus.Accepted;
                            break;

                        case ActiveAssignmentStatus.Applied:
                            setInactiveStatus = InactiveAssignmentStatus.Applied;
                            break;

                        case ActiveAssignmentStatus.AttendanceRecorded:
                            setInactiveStatus = InactiveAssignmentStatus.AttendanceRecorded;
                            break;

                        case ActiveAssignmentStatus.Confirmed:
                            setInactiveStatus = InactiveAssignmentStatus.Confirmed;
                            break;

                        case ActiveAssignmentStatus.Contract:
                            setInactiveStatus = InactiveAssignmentStatus.Contract;
                            break;

                        case ActiveAssignmentStatus.Declined:
                            setInactiveStatus = InactiveAssignmentStatus.Declined;
                            break;

                        case ActiveAssignmentStatus.Invited:
                            setInactiveStatus = InactiveAssignmentStatus.Invited;
                            break;

                        case ActiveAssignmentStatus.Selected:
                            setInactiveStatus = InactiveAssignmentStatus.Selected;
                            break;

                        default:
                            setInactiveStatus = InactiveAssignmentStatus.Selected;
                            break;
                    }
 
                    SetStateRequest setStateRequest = new SetStateRequest()
                    {
                        EntityMoniker = new EntityReference
                        {
                            Id = (Guid)assignment.GetAttributeValue<AliasedValue>("educ_assignment1.educ_assignmentid").Value,
                            LogicalName = "educ_assignment",
                        },
                        State = new OptionSetValue((int)State.Inactive),
                        Status = new OptionSetValue((int)setInactiveStatus)
                    };

                    crmWorkflowContext.OrganizationService.Execute(setStateRequest);
                }

            }
        }
    }
}