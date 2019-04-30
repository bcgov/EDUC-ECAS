using System;
using System.Collections.Generic;
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

            //services.AddAuthentication(BasicAuthenticationDefaults.AuthenticationScheme)
            //    .AddBasicAuthentication(credentials =>
            //        Task.FromResult(
            //            credentials.username == "myUsername"
            //            && credentials.password == "myPassword"));

            services.AddAuthentication(BasicAuthenticationDefaults.AuthenticationScheme)
                .AddBasicAuthentication(credentials =>
                    Task.FromResult(
                        credentials.username == "ecasadmin"
                        && credentials.password == "Ec@s201p!"));
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
