using Microsoft.Crm.Sdk.Messages;
using Microsoft.Xrm.Sdk;
using Microsoft.Xrm.Sdk.Query;
using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace Ecas.Dyn365.Workflows.Utils
{
    public class Assigment
    {
        Guid assignmentId;
        IOrganizationService organizationService;
        ITracingService tracingService;

        public Assigment(Guid _assignmentId, IOrganizationService _organizationService, ITracingService _tracingService)
        {
            if (_assignmentId == Guid.Empty) throw new ArgumentNullException("Assignment Id cannot be null");
            if (_organizationService == null) throw new ArgumentNullException("Organization Service cannot be null");
            if (_tracingService == null) throw new ArgumentNullException("Tracing Service Id cannot be null");

            assignmentId = _assignmentId;
            organizationService = _organizationService;
            tracingService = _tracingService;

            tracingService.Trace("Loaded Assignment Util");
        }

        public void GeneratedPaymentRecords()
        {
            var approvedPayments = GetNonSupplementalExpenseRecords();
            tracingService.Trace("Fetched Expense Records");

            if (approvedPayments.Entities.ToList().Count() == 0) return;
            //Process Non-Supplemental Payments
            var fees = approvedPayments.Entities.Where(e => e.GetAttributeValue<string>("educ_name") == "Fee").ToList();
            GenerateFeeBasedPaymentRecords(fees);
            var regularExpenses = approvedPayments.Entities.Where(e => e.GetAttributeValue<string>("educ_name") != "Fee").ToList();
            GenerateExpenseBasedPaymentRecords(regularExpenses);

            //Process Supplemental Payments
            var approvedSupplementalExpenses = GetSupplementalExpenseRecords().Entities.ToList();
            UpdateSupplementalExpenses(approvedSupplementalExpenses);
        }

        private int GenerateFeeBasedPaymentRecords(List<Entity> fees)
        {
            foreach (var expense in fees)
                CreatePaymentRecord(610410000, expense.GetAttributeValue<Money>("educ_amount").Value, expense);

            tracingService.Trace("Generated {0} Fee Payment Records", fees.Count());
            return fees.Count();
        }

        private int GenerateExpenseBasedPaymentRecords(List<Entity> regularExpenses)
        {
            decimal totalamount = 0;

            foreach (var expense in regularExpenses)
                totalamount += expense.GetAttributeValue<Money>("educ_amount").Value;

            if (totalamount > 0)
                CreatePaymentRecord(610410001, totalamount, regularExpenses);
            tracingService.Trace("Generated {0} Regular Expense Payment Records", regularExpenses.Count());
            return regularExpenses.Count();
        }

        private EntityCollection GetNonSupplementalExpenseRecords()
        {
            QueryExpression qx = new QueryExpression();
            qx.EntityName = "educ_exoense";
            qx.ColumnSet.AllColumns = true;
            qx.Criteria.AddCondition("educ_assignment", ConditionOperator.Equal, assignmentId);
            //Approved Expenses Only
            qx.Criteria.AddCondition("statuscode", ConditionOperator.Equal, 610410001);
            //Supplemental  != YES
            qx.Criteria.AddCondition("educ_supplementalexpense", ConditionOperator.NotEqual, 610410000);

            return organizationService.RetrieveMultiple(qx);
        }

        private EntityCollection GetSupplementalExpenseRecords()
        {
            QueryExpression qx = new QueryExpression();
            qx.EntityName = "educ_exoense";
            qx.ColumnSet.AllColumns = true;
            qx.Criteria.AddCondition("educ_assignment", ConditionOperator.Equal, assignmentId);
            //Approved Expenses Only
            qx.Criteria.AddCondition("statuscode", ConditionOperator.Equal, 610410001);
            //Supplemental  == YES 
            qx.Criteria.AddCondition("educ_supplementalexpense", ConditionOperator.Equal, 610410000);

            return organizationService.RetrieveMultiple(qx);
        }

        private Guid CreatePaymentRecord(int paymentType, decimal paymentAmount, List<Entity> relatedExpenses)
        {
            //Create Payment record
            Entity payment = new Entity("educ_payment");
            payment["educ_assignment"] = new EntityReference("educ_assignment", assignmentId);
            payment["educ_paymenttype"] = new OptionSetValue(paymentType);
            payment["educ_amount"] = new Money(paymentAmount);
            payment["statuscode"] = new OptionSetValue(1);

            var paymentId = organizationService.Create(payment);

            //Update Non-Supplemental Expense Records with reference to payment record

            foreach (var expense in relatedExpenses)
            {
                expense["educ_payment"] = new EntityReference("educ_payment", paymentId);
                expense["statuscode"] = new OptionSetValue(610410002);

                organizationService.Update(expense);
            }

            return paymentId;
        }

        private Guid CreatePaymentRecord(int paymentType, decimal paymentAmount, Entity fee)
        {
            var feeList = new List<Entity>();
            feeList.Add(fee);
            return CreatePaymentRecord(paymentType, paymentAmount, feeList);
        }

        private void UpdateSupplementalExpenses(List<Entity> supplementalExpenses)
        {
            //Update Expense Records with reference to payment record
            var stateCode = new OptionSetValue();
            var statusCode = new OptionSetValue();

            foreach (var expense in supplementalExpenses)
            {
                SetStateRequest setStateRequest = new SetStateRequest()
                {
                    EntityMoniker = new EntityReference
                    {
                        Id = expense.Id,
                        LogicalName = "educ_exoense",
                    },
                    State = new OptionSetValue(1),
                    Status = new OptionSetValue(610410006)
                };
                organizationService.Execute(setStateRequest);

                //If not suplemental, set statuscode to Suplemental Recorded
                //if (expense.GetAttributeValue<OptionSetValue>("educ_supplementalexpense").Value == 610410000)
                //    expense["statuscode"] = new OptionSetValue(610410006);

                //organizationService.Update(expense);
            }
        }
    }
}
