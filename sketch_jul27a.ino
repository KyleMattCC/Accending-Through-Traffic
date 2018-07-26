char receiveVal;
void setup()  
{   
  
  Serial.begin(9600);    
}  
  
void loop()  
{   
  if(Serial.available() > 0)  
  {       
      Serial.print("0");   
       receiveVal = Serial.read();    
  }  
  delay(200);
  Serial.print(receiveVal);  
  delay(200);   
}  

