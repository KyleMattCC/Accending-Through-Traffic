using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.IO.Ports;
using TrafficInterface;
using System.Net;
using System.IO;
using Newtonsoft.Json;
using System.Net.Http;
using System.Net.Http.Headers;


namespace TrafficInterface
{
    class Program
    {
        static void Main(string[] args)
        {


            /*string username = "john";
            string urlAddress = "http://127.0.0.1/csharp.php?username=" + username;

            using (WebClient client = new WebClient())
            {
                // this string contains the webpage's source
                string pagesource = client.DownloadString(urlAddress);
                Console.Write(pagesource);
            }*/

            ArduinoSerial serial = new ArduinoSerial();
            string urlAddress;
            string intersection;
            string command;

            serial.WriteToArduino("0");
            using(WebClient client = new WebClient())
            {
                while (true)
                {
                    intersection = Convert.ToString(serial.ReadFromArduino());
                    urlAddress = "http://127.0.0.1/TrafficAnalyzer.php?intersection=" + intersection;
                    command = client.DownloadString(urlAddress);
                    Console.Write(command);
                    serial.WriteToArduino(command);
                }
            }

 
        }
    }
}
