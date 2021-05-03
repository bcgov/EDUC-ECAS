using System;
using System.IO;
using System.Runtime.Serialization.Json;
using System.Text;
using System.Activities;
using Microsoft.Xrm.Sdk;
using Microsoft.Xrm.Sdk.Workflow;
using Ecas.Dyn365.CDOGSPDFConverter.Helper;
using Ecas.Dyn365.CDOGSPDFConverter.Models;

namespace Ecas.Dyn365.Workflows
{
    public class CDOGSPDFGenerator : WorkFlowActivityBase
    {
        [RequiredArgument]
        [Input("Assignment")]
        [ReferenceTarget("educ_assignment")]
        public InArgument<EntityReference> Assignment { get; set; }

        public override void ExecuteCRMWorkFlowActivity(CodeActivityContext context, LocalWorkflowContext crmWorkflowContext)
        {
            crmWorkflowContext.TracingService.Trace("CDOGS PDF converter has been started!");

            //Read the Assignment Id
            var assignmentId = Assignment.Get<EntityReference>(context).Id;

            //Fetch configurations from Configuration Entity in Dynamics to prepare for HTTP API Call
            var configs = Helper.GetSystemConfigurations(crmWorkflowContext.OrganizationService, ConfigConstants.CONTRACT, string.Empty);
            string authURL = Helper.GetConfigKeyValue(configs, ConfigConstants.AUTH_URL, ConfigConstants.CONTRACT);
            string cdogsURL = Helper.GetConfigKeyValue(configs, ConfigConstants.CDOGS_URL, ConfigConstants.CONTRACT);
            string authKey = Helper.GetConfigKeyValue(configs, ConfigConstants.AUTH_KEY, ConfigConstants.CONTRACT);
            
            Uri authUri = new Uri(authURL, UriKind.Absolute);
            Uri cdogsUri = new Uri(cdogsURL, UriKind.Absolute);

            string token = null;
            var result = Helper.GetToken(authKey,authUri).Result;
            
            //extract the token
            using (var ms = new MemoryStream(Encoding.Unicode.GetBytes(result)))
            {
                DataContractJsonSerializer deserializer = new DataContractJsonSerializer(typeof(NestData));
                NestData bsObj2 = (NestData)deserializer.ReadObject(ms);
                token = bsObj2.access_token;
            }

            //start setting the parameters of the new file
            var documentdetails = Helper.GetContent(crmWorkflowContext.OrganizationService, assignmentId);

            string fileName = documentdetails.Item1;
            string documentBody = documentdetails.Item2;


            //setting the file extension 
            var pdfFileName = Path.ChangeExtension(fileName, ".pdf");
            byte[] doc = Helper.ConvertDoc(token, documentBody, cdogsUri).Result;
            string encodedData = Convert.ToBase64String(doc);

            Entity AnnotationEntityObject = new Entity("annotation");

            //associate the annotation to the assignment then set filename, content etc. details
            EntityReference entityReference = new EntityReference("educ_assignment", assignmentId);
            AnnotationEntityObject.Attributes["filename"] = pdfFileName;
            AnnotationEntityObject.Attributes["subject"] = pdfFileName;
            AnnotationEntityObject.Attributes["notetext"] = pdfFileName;
            AnnotationEntityObject.Attributes["documentbody"] = encodedData;
            AnnotationEntityObject.Attributes["objectid"] = entityReference;
            AnnotationEntityObject.Attributes["objecttypecode"] = "educ_assignment";
            AnnotationEntityObject.Attributes["mimetype"] = @"application\pdf";
            crmWorkflowContext.OrganizationService.Create(AnnotationEntityObject);
        }  
    }
}