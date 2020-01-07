using Ecas.Dyn365.CAS.ScheduledJob.ScheduleJobSession;
using Microsoft.Extensions.Configuration;
using System;
using System.IO;

namespace Ecas.Dyn365.CAS.ScheduledJob
{
    class Program
    {
        static void Main(string[] args)
        {
            Console.WriteLine("Fetching Payments currently being processed by CAS");

            var builder = new ConfigurationBuilder()
                .SetBasePath(Directory.GetCurrentDirectory())
                .AddJsonFile("appsettings.json", optional: true, reloadOnChange: true);

            IConfigurationRoot configuration = builder.Build();
            var webapiUrl = configuration.GetSection("WebapiUrl");
            var userName = configuration.GetSection("UserName");
            var password = configuration.GetSection("Password");

            var checkCASPaymentUtil = new CheckPaymentStatusLogic(webapiUrl.Value, userName.Value,
                password.Value);
            Console.WriteLine(checkCASPaymentUtil.VerifyStatusOfInProgressPayments());

            Console.ReadLine();
        }
    }
}
