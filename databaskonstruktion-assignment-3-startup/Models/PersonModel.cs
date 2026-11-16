using System.Data;
using MySql.Data.MySqlClient;

namespace mvc_multiple_controllers.Models
{
    public class PersonModel
    {
        private readonly IConfiguration _configuration;
        private readonly string _connectionString;

        public PersonModel(IConfiguration configuration)
        {
            _configuration = configuration;
            _connectionString = _configuration["SchoolConnection"] 
                ?? Environment.GetEnvironmentVariable("SchoolConnection")
                ?? "Server=mysql;Port=3306;Database=a25milha;User ID=dbkonstruktion;Password=<REDACTED>;Pooling=false;SslMode=Required;convert zero datetime=True;";
        }

        public DataTable GetAllPersons()
        {
            using var dbcon = new MySqlConnection(_connectionString);
            dbcon.Open();
            using var adapter = new MySqlDataAdapter("SELECT * FROM Person;", dbcon);
            var ds = new DataSet();
            adapter.Fill(ds, "result");
            return ds.Tables["result"] ?? new DataTable();
        }

        public DataTable GetAvgSakerhet()
        {
            using var dbcon = new MySqlConnection(_connectionString);
            dbcon.Open();
            using var adapter = new MySqlDataAdapter("CALL GETAVGSAKERHET();", dbcon);
            var ds = new DataSet();
            adapter.Fill(ds, "result");
            return ds.Tables["result"] ?? new DataTable();
        }

        public DataTable SearchPersons(string name)
        {
            using var dbcon = new MySqlConnection(_connectionString);
            dbcon.Open();
            using var adapter = new MySqlDataAdapter("SELECT * FROM Person WHERE Namn LIKE @NAME;", dbcon);
            adapter.SelectCommand.Parameters.AddWithValue("@NAME", "%" + name + "%");
            var ds = new DataSet();
            adapter.Fill(ds, "result");
            return ds.Tables["result"] ?? new DataTable();
        }
    }
}
