using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.IO.Ports;
using System.Threading;

namespace TrafficInterface
{
    public class ArduinoSerial
    {
        private SerialPort serialPort;

        public ArduinoSerial()
        {
            serialPort = new SerialPort("COM3", 9600, Parity.None, 8);
            serialPort.Open();
        }

        public char ReadFromArduino()
        {
            char data = Convert.ToChar(serialPort.ReadByte());
            return data;

        }

        public void WriteToArduino(string data)
        {
            serialPort.Write(data);
        }
    }
}
