using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace Ecas.Dyn365.Workflows.Models
{
    public class Supplier
    {
        public string LastName { get; set; }
        public string SupplierNumber { get; set; }
        public string SupplierSiteNumber { get; set; }
        public string MethodOfPayment { get; set; }
    }
}
