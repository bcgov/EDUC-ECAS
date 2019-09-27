using System;
using System.Collections.Generic;
using System.Linq;
using System.Threading.Tasks;

namespace Ecas.Dyn365Service.Utils
{
    public class Dynamics365OptionSet
    {
        public string InternalName { get; set; }
        public string LogicalName { get; set; }

        public List<Dynamics365OptionSetItem> Options { get; set; }
    }

    public class Dynamics365OptionSetItem
    {
        public int Id { get; set; }
        public string Label {get;set;}
    }
}
