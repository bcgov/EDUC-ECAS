using System;
using System.Collections.Generic;
using System.IO;
using System.Linq;
using System.Threading.Tasks;
using Bazinga.AspNetCore.Authentication.Basic;
using Ecas.Dyn365Service.Utils;
using Microsoft.AspNetCore.Builder;
using Microsoft.AspNetCore.Hosting;
using Microsoft.AspNetCore.HttpsPolicy;
using Microsoft.AspNetCore.Mvc;
using Microsoft.Extensions.Configuration;
using Microsoft.Extensions.DependencyInjection;
using Microsoft.Extensions.Logging;
using Microsoft.Extensions.Options;

namespace Ecas.Dyn365Service
{
    public class Startup
    {
        public Startup(IConfiguration configuration)
        {
            Configuration = configuration;
        }

        public IConfiguration Configuration { get; }

        // This method gets called by the runtime. Use this method to add services to the container.
        public void ConfigureServices(IServiceCollection services)
        {
            services.AddMvc().SetCompatibilityVersion(CompatibilityVersion.Version_2_1);

            services.AddScoped<DynamicsAuthenticationSettings>();
            services.AddScoped<ECasAPISecuritySettings>();

            //Load Config Keys for ApiSecurity parameters
            var builder = new ConfigurationBuilder()
                                        .SetBasePath(Directory.GetCurrentDirectory())
                                        .AddJsonFile("appsettings.json", optional: true, reloadOnChange: true)
                                        .AddEnvironmentVariables();

            var configuration = builder.Build();
            var ecasAPISecuritySettingsSection = configuration.GetSection("ECasAPISecuritySettings");
            var ecasAPISecuritySettingsSettings = ecasAPISecuritySettingsSection.Get<ECasAPISecuritySettings>();

            services.AddAuthentication(BasicAuthenticationDefaults.AuthenticationScheme)
                .AddBasicAuthentication(credentials =>
                    Task.FromResult(
                        credentials.username == ecasAPISecuritySettingsSettings.UserName && 
                        credentials.password == ecasAPISecuritySettingsSettings.Password));
        }

        // This method gets called by the runtime. Use this method to configure the HTTP request pipeline.
        public void Configure(IApplicationBuilder app, IHostingEnvironment env)
        {
            if (env.IsDevelopment())
            {
                app.UseDeveloperExceptionPage();
            }
            else
            {
                app.UseHsts();
            }

            app.UseHttpsRedirection();
            app.UseAuthentication();
            app.UseMvc();
        }
    }
}
