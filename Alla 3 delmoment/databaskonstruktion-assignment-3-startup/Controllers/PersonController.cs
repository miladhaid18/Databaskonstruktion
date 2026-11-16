using Microsoft.AspNetCore.Mvc;
using Microsoft.Extensions.Configuration;
using mvc_multiple_controllers.Models;

namespace mvc_multiple_controllers.Controllers
{
    public class PersonController : Controller
    {
        private readonly IConfiguration _configuration;
        private PersonModel _personModel;

        public PersonController(IConfiguration configuration)
        {
            _configuration = configuration;
            _personModel = new PersonModel(_configuration);
        }

        public IActionResult Index()
        {
            ViewBag.PersonTable = _personModel.GetAllPersons();
            ViewBag.AvgSakerhet = _personModel.GetAvgSakerhet();
            return View();
        }

        public IActionResult SearchPersons(string name)
        {
            ViewBag.SearchResults = _personModel.SearchPersons(name);
            return View();
        }
    }
}
