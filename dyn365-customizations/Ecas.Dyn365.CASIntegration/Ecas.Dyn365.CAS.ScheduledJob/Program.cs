using Ecas.Dyn365.CAS.ScheduledJob.ScheduleJobSession;
using System;

namespace Ecas.Dyn365.CAS.ScheduledJob
{
    class Program
    {
        static void Main(string[] args)
        {
            Console.WriteLine("Fetching Payments currently being processed by CAS");

            var checkCASPaymentUtil = new CheckPaymentStatusLogic();
            checkCASPaymentUtil.VerifyStatusOfInProgressPayments();

            Console.ReadLine();
        }
    }
}
