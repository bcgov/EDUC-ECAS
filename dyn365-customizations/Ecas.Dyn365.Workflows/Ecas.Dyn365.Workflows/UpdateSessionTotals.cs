using Microsoft.Xrm.Sdk;
using Microsoft.Xrm.Sdk.Query;
using Microsoft.Xrm.Sdk.Workflow;
using System;
using System.Activities;


namespace Ecas.Dyn365.Workflows
{
    public class CalculateSessionSubTotals : WorkFlowActivityBase
    {
        [Input("Expense")]
        [ReferenceTarget("educ_exoense")]
        public InArgument<EntityReference> Exoense { get; set; }

        public enum ExpenseStatus
        {
            Paid = 2,
            SupplementalComplete = 610410006
        }

        public enum SupplementalExpenseStatus
        {
            No = 610410001,
            Yes = 610410000
        }

        public override void ExecuteCRMWorkFlowActivity(CodeActivityContext context, LocalWorkflowContext crmWorkflowContext)
        {
            Guid sessionId;
            EntityCollection expenseRecords = new EntityCollection();
            EntityReference expense = Exoense.Get(context);
            Guid expenseId = expense.Id;
            crmWorkflowContext.TracingService.Trace($"ExpenseID: {expenseId}");
            var fetchXml = $@"
            <fetch top='1'>
              <entity name='educ_exoense'>
                <filter>
                  <condition attribute='educ_exoenseid' operator='eq' value='{expenseId}'/>
                </filter>
                <link-entity name='educ_assignment' from='educ_assignmentid' to='educ_assignment'>
                  <link-entity name='educ_session' from='educ_sessionid' to='educ_session'>
                    <attribute name='educ_sessionid' />
                  </link-entity>
                </link-entity>
              </entity>
            </fetch>";

            EntityCollection sessions = crmWorkflowContext.OrganizationService.RetrieveMultiple(new FetchExpression(fetchXml));
            if (sessions.Entities.Count > 0)
            {
                sessionId = (Guid)sessions.Entities[0].GetAttributeValue<AliasedValue>("educ_session2.educ_sessionid").Value;
                QueryExpression QEeduc_exoense = new QueryExpression("educ_exoense");
                QEeduc_exoense.ColumnSet.AllColumns = true;
                var QEeduc_exoense_educ_assignment = QEeduc_exoense.AddLink("educ_assignment", "educ_assignment", "educ_assignmentid");
                QEeduc_exoense_educ_assignment.EntityAlias = "assignment";
                var QEeduc_exoense_educ_assignment_educ_session = QEeduc_exoense_educ_assignment.AddLink("educ_session", "educ_session", "educ_sessionid");
                QEeduc_exoense_educ_assignment_educ_session.EntityAlias = "session";
                QEeduc_exoense_educ_assignment_educ_session.LinkCriteria.AddCondition("educ_sessionid", ConditionOperator.Equal, sessionId);
                expenseRecords = crmWorkflowContext.OrganizationService.RetrieveMultiple(QEeduc_exoense);
                decimal totalFeesPaid = 0;
                decimal totalPaidExpensesNonSupplemental = 0;
                decimal totalPaidExpensesSupplemental = 0;
                decimal ExpenseAmount;
                string Expense;
                int StatusReason;
                int SupplementalExpense;

                foreach (var expenseRecord in expenseRecords.Entities)
                {
                    StatusReason = (int)expenseRecord.GetAttributeValue<OptionSetValue>("statuscode").Value;
                    SupplementalExpense = (int)expenseRecord.GetAttributeValue<OptionSetValue>("educ_supplementalexpense").Value;
                    Expense = expenseRecord.GetAttributeValue<string>("educ_name");
                    ExpenseAmount = expenseRecord.GetAttributeValue<Money>("educ_amount").Value;

                    if (Expense == "Fee" && StatusReason == (int)ExpenseStatus.Paid)
                    {
                        totalFeesPaid += ExpenseAmount;
                    }

                    if (Expense != "Fee" && StatusReason == (int)ExpenseStatus.Paid && SupplementalExpense == (int)SupplementalExpenseStatus.No)
                    {
                        totalPaidExpensesNonSupplemental += ExpenseAmount;
                    }

                    if (Expense != "Fee" && StatusReason == (int)ExpenseStatus.SupplementalComplete && SupplementalExpense == (int)SupplementalExpenseStatus.Yes)
                    {
                        totalPaidExpensesSupplemental += ExpenseAmount;
                    }
                }
             
                crmWorkflowContext.TracingService.Trace($"Found Expense ID: {expenseId}");
                
                crmWorkflowContext.TracingService.Trace($"Found Session ID: {sessionId}");

                crmWorkflowContext.TracingService.Trace($"TotalFees: {totalFeesPaid}");
                crmWorkflowContext.TracingService.Trace($"TotalNonSupplemental: {totalPaidExpensesNonSupplemental}");
                crmWorkflowContext.TracingService.Trace($"TotalSupplemental: {totalPaidExpensesSupplemental}");
                Entity SessionToUpdate = new Entity("educ_session");
                SessionToUpdate.Id = sessionId;
                SessionToUpdate["educ_totalfeespaid0"] = new Money(totalFeesPaid);
                SessionToUpdate["educ_totalpaidexpensesnonsupplemental0"] = new Money(totalPaidExpensesNonSupplemental);
                SessionToUpdate["educ_totalpaidexpensessupplemental0"] = new Money(totalPaidExpensesSupplemental);
                crmWorkflowContext.OrganizationService.Update(SessionToUpdate);
             }
            else
            {
                crmWorkflowContext.TracingService.Trace($"No associated sessions; exiting...");
            }
        }
    }
}