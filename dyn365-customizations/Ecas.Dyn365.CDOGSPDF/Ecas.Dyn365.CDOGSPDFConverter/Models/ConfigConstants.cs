using System.Runtime.Serialization;

namespace Ecas.Dyn365.CDOGSPDFConverter.Models
{
    [DataContract]
    public class NestData
    {
        [DataMember]
        public string access_token;
    }

    class ConfigConstants
    {
        public static string CONTRACT = "CONTRACT";
        public static string AUTH_URL = "CDOGS-AUTH-URL";
        public static string CDOGS_URL = "CDOGS-URL";
        public static string AUTH_KEY = "AUTH-KEY";
    }
}

