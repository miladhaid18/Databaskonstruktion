using System.Data;
using MySql.Data.MySqlClient;

namespace mvc_multiple_controllers.Models
{
    public class ObservationModel
    {
        private readonly IConfiguration _configuration;
        private readonly string _connectionString;

        public ObservationModel(IConfiguration configuration)
        {
            _configuration = configuration;
            _connectionString = _configuration["SchoolConnection"] 
                ?? Environment.GetEnvironmentVariable("SchoolConnection")
                ?? "Server=mysql;Port=3306;Database=a25milha;User ID=dbkonstruktion;Password=<REDACTED>;Pooling=false;SslMode=Required;convert zero datetime=True;";
        }

        public DataTable GetObservationsWithDetails()
        {
            using var dbcon = new MySqlConnection(_connectionString);
            dbcon.Open();
            using var adapter = new MySqlDataAdapter("SELECT o.IncidentId, o.IncidentNamn, p.PlatsNamn, o.ObsId, o.Datum, o.Sakerhet, g.Beskrivning AS Grad, CONCAT(a.Fornamn, ' ', a.Efternamn) AS Handlaggare FROM Observation o JOIN Plats p ON o.PlatsKod = p.PlatsKod JOIN GradBeskrivning g ON o.GradKod = g.GradKod JOIN Agent a ON o.RegistreradAvNamn = a.AgentNamn AND o.RegistreradAvNr = a.AgentNr ORDER BY o.IncidentId, o.ObsId;", dbcon);
            var ds = new DataSet();
            adapter.Fill(ds, "result");
            return ds.Tables["result"] ?? new DataTable();
        }

        public DataTable GetAllPlats()
        {
            using var dbcon = new MySqlConnection(_connectionString);
            dbcon.Open();
            using var adapter = new MySqlDataAdapter("SELECT * FROM Plats;", dbcon);
            var ds = new DataSet();
            adapter.Fill(ds, "result");
            return ds.Tables["result"] ?? new DataTable();
        }

        public DataTable GetAllHandlaggare()
        {
            using var dbcon = new MySqlConnection(_connectionString);
            dbcon.Open();
            using var adapter = new MySqlDataAdapter("SELECT a.AgentNamn, a.AgentNr, CONCAT(a.Fornamn, ' ', a.Efternamn) AS Namn FROM Agent a JOIN Handlaggare h ON a.AgentNamn = h.AgentNamn AND a.AgentNr = h.AgentNr;", dbcon);
            var ds = new DataSet();
            adapter.Fill(ds, "result");
            return ds.Tables["result"] ?? new DataTable();
        }

        public void InsertObservation(string obsid, string incidentid, string incidentnamn, string platskod, string gradkod, string sakerhet, string regnamn, string regnr)
        {
            if (string.IsNullOrWhiteSpace(obsid) || !int.TryParse(obsid, out _))
                throw new ArgumentException("ObsId måste vara ett heltal");
            if (string.IsNullOrWhiteSpace(incidentid) || !int.TryParse(incidentid, out _))
                throw new ArgumentException("IncidentId måste vara ett heltal");
            if (string.IsNullOrWhiteSpace(incidentnamn))
                throw new ArgumentException("Incidentnamn får inte vara tomt");
            if (string.IsNullOrWhiteSpace(platskod) || !int.TryParse(platskod, out _))
                throw new ArgumentException("Platskod måste vara ett heltal");
            if (string.IsNullOrWhiteSpace(gradkod) || !int.TryParse(gradkod, out var grad) || grad < 1 || grad > 4)
                throw new ArgumentException("Grad måste vara ett heltal mellan 1 och 4");
            if (string.IsNullOrWhiteSpace(sakerhet) || !decimal.TryParse(sakerhet, out var sak) || sak < 1 || sak > 114)
                throw new ArgumentException("Säkerhet måste vara ett tal mellan 1 och 114");
            if (string.IsNullOrWhiteSpace(regnamn) || string.IsNullOrWhiteSpace(regnr) || !int.TryParse(regnr, out _))
                throw new ArgumentException("Handläggare måste väljas");

            using var dbcon = new MySqlConnection(_connectionString);
            dbcon.Open();
            const string insertString = "INSERT INTO Observation(ObsId, Datum, GradKod, Sakerhet, IncidentId, IncidentNamn, PlatsKod, RegistreradAvNamn, RegistreradAvNr) VALUES(@OBSID, NOW(), @GRAD, @SAKERHET, @INCID, @INCNAMN, @PLATS, @REGNAMN, @REGNR);";
            using var sqlCmd = new MySqlCommand(insertString, dbcon);
            sqlCmd.Parameters.AddWithValue("@OBSID", obsid);
            sqlCmd.Parameters.AddWithValue("@GRAD", gradkod);
            sqlCmd.Parameters.AddWithValue("@SAKERHET", sakerhet);
            sqlCmd.Parameters.AddWithValue("@INCID", incidentid);
            sqlCmd.Parameters.AddWithValue("@INCNAMN", incidentnamn);
            sqlCmd.Parameters.AddWithValue("@PLATS", platskod);
            sqlCmd.Parameters.AddWithValue("@REGNAMN", regnamn);
            sqlCmd.Parameters.AddWithValue("@REGNR", regnr);
            sqlCmd.ExecuteNonQuery();
        }

        public void UpdateSakerhet(string obsid, string sakerhet)
        {
            using var dbcon = new MySqlConnection(_connectionString);
            dbcon.Open();
            const string updateString = "UPDATE Observation SET Sakerhet = @SAKERHET WHERE ObsId = @OBSID;";
            using var sqlCmd = new MySqlCommand(updateString, dbcon);
            sqlCmd.Parameters.AddWithValue("@SAKERHET", sakerhet);
            sqlCmd.Parameters.AddWithValue("@OBSID", obsid);
            sqlCmd.ExecuteNonQuery();
        }

        public void ArkiveraObservation(string obsid)
        {
            using var dbcon = new MySqlConnection(_connectionString);
            dbcon.Open();
            using var sqlCmd = new MySqlCommand("CALL ARKIVERAOBSERVATION(@OBSID);", dbcon);
            sqlCmd.Parameters.AddWithValue("@OBSID", obsid);
            sqlCmd.ExecuteNonQuery();
        }

        public DataTable SearchObservationsByPlats(string platskod)
        {
            using var dbcon = new MySqlConnection(_connectionString);
            dbcon.Open();
            using var adapter = new MySqlDataAdapter("SELECT o.IncidentId, o.IncidentNamn, p.PlatsNamn, o.ObsId, o.Datum, o.Sakerhet FROM Observation o JOIN Plats p ON o.PlatsKod = p.PlatsKod WHERE o.PlatsKod = @PLATSKOD ORDER BY o.ObsId;", dbcon);
            adapter.SelectCommand.Parameters.AddWithValue("@PLATSKOD", platskod);
            var ds = new DataSet();
            adapter.Fill(ds, "result");
            return ds.Tables["result"] ?? new DataTable();
        }
    }
}
