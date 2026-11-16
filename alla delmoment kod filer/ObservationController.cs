using Microsoft.AspNetCore.Mvc;
using Microsoft.Extensions.Configuration;
using mvc_multiple_controllers.Models;

namespace mvc_multiple_controllers.Controllers
{
    public class ObservationController : Controller
    {
        private readonly IConfiguration _configuration;
        private ObservationModel _observationModel;

        public ObservationController(IConfiguration configuration)
        {
            _configuration = configuration;
            _observationModel = new ObservationModel(_configuration);
        }

        public IActionResult Index()
        {
            ViewBag.ObsTable = _observationModel.GetObservationsWithDetails();
            ViewBag.AllPlats = _observationModel.GetAllPlats();
            ViewBag.AllHandlaggare = _observationModel.GetAllHandlaggare();
            return View();
        }

        public IActionResult SearchObservations(string platskod)
        {
            ViewBag.SearchResults = _observationModel.SearchObservationsByPlats(platskod);
            return View();
        }

        public IActionResult InsertObservation(string obsid, string incidentid, string incidentnamn, string platskod, string gradkod, string sakerhet, string regnamn, string regnr)
        {
            try
            {
                _observationModel.InsertObservation(obsid, incidentid, incidentnamn, platskod, gradkod, sakerhet, regnamn, regnr);
                TempData["Success"] = "Observation registrerad.";
            }
            catch (ArgumentException ex)
            {
                TempData["Error"] = ex.Message;
            }
            return RedirectToAction("Index");
        }

        public IActionResult UpdateSakerhet(string obsid, string sakerhet)
        {
            _observationModel.UpdateSakerhet(obsid, sakerhet);
            return RedirectToAction("Index");
        }

        public IActionResult ArkiveraObservation(string obsid)
        {
            _observationModel.ArkiveraObservation(obsid);
            return RedirectToAction("Index");
        }
    }
}
