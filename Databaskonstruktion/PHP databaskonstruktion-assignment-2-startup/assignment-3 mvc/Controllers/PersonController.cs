using Microsoft.AspNetCore.Mvc;
using Microsoft.Extensions.Configuration;
using mvc_multiple_controllers.Models;

namespace mvc_multiple_controllers.Controllers
{
    public class PersonController : Controller
    {
        private readonly IConfiguration _configuration;

        public PersonController(IConfiguration configuration)
        {
            _configuration = configuration;
        }

        public IActionResult Index()
        {
            ViewBag.SomeText = "Personer kommer visas har i nasta steg";
            return View();
        }
    }
}
