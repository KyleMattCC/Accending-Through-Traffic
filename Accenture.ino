//Left LED Digit
int leftLEDNumber[10][7] = {
                    {0, 0, 0, 0, 0, 0, 0}, //Blank
                    {1, 0, 0, 0, 0, 0, 1}, //1
                    {0, 1, 1, 1, 1, 0, 1}, //2
                    {1, 0, 1, 1, 1, 0, 1}, //3
                    {1, 0, 0, 1, 0, 1, 1}, //4
                    {1, 0, 1, 1, 1, 1, 0}, //5
                    {1, 1, 1, 1, 1, 1, 0}, //6
                    {1, 0, 0, 0, 1, 0, 1}, //7
                    {1, 1, 1, 1, 1, 1, 1}, //8
                    {1, 0, 1, 1, 1, 1, 1}, //9
                  };
                  
//Right LED Digit           
int rightLEDNumber[10][7]{
                    {1, 1, 1, 1, 1, 1, 0}, //0
                    {0, 0, 1, 1, 0, 0, 0}, //1      
                    {1, 1, 0, 1, 0, 1, 1}, //2
                    {1, 0, 1, 1, 0, 1, 1}, //3
                    {0, 0, 1, 1, 1, 0, 1}, //4
                    {1, 0, 1, 0, 1, 1, 1}, //5
                    {1, 1, 1, 0, 1, 1, 1}, //6
                    {0, 0, 1, 1, 0, 1, 0}, //7
                    {1, 1, 1, 1, 1, 1, 1}, //8
                    {1, 0, 1, 1, 1, 1, 1}, //9
                  };

char command;
void setup() {
  // put your setup code here, to run once:
  Serial.begin(9600);
  int i;
  
  for(i = 2; i <= 17; i++){
    pinMode(i, OUTPUT);
  }
  pinMode(A2, OUTPUT);
  pinMode(A3, OUTPUT);
}

void changeLight(int intersection){
  //intersection 0 should turn green
  if(intersection == 0){
    //Intersection 1 is orange; 0
    digitalWrite(A2, 0);
    digitalWrite(A3, 0);
    delay(1000);
    //Intersection 1 is red; 1
    //Intersection 0 is green
    digitalWrite(A2, 0);
    digitalWrite(A3, 1);
  }

  //intersection 1 should turn green
  else if(intersection == 1){
    //Intersection 0 is orange; 2
    digitalWrite(A2, 1);
    digitalWrite(A3, 0);
    delay(1000);
    //Intersection 0 is red; 3
    //Intersection 1 is green
    digitalWrite(A2, 1);
    digitalWrite(A3, 1);
  }
}

void countDown(int intersection, int secs){
  int tens = secs/10;
  int ones = secs%10;
  int tensCtr;
  int onesCtr;
  int ctr;
  
  for(tensCtr = tens; tensCtr >= 0; tensCtr--){
    //displays tens digit
    for(ctr = 0; ctr < 7; ctr++){
      digitalWrite(ctr+2, leftLEDNumber[tensCtr][ctr]);      
    }
        
    for(onesCtr = ones; onesCtr >= 0; onesCtr--){
      //display ones digit
      for(ctr = 0; ctr < 7; ctr++)  {
        digitalWrite(ctr+9, rightLEDNumber[onesCtr][ctr]);
      }
      if(secs == 1){
        if(intersection == 0)
          Serial.write("1");
        else if(intersection == 1)
          Serial.write("0");
      }
      delay(1000);
      secs--;
    }
    ones = 9;
  }
}

void stoplightFunc(int intersection, char traffic){
  changeLight(intersection);
  if(traffic == '0')
    countDown(intersection, 5);
  else if(traffic == '1')
    countDown(intersection, 10);
  else
    countDown(intersection, 15);
}

void loop() {
  // get stoplight decision for intersection 0
  // 0 - Light
  // 1 - Moderate
  // 2 - Heavy
  while(Serial.available() == 0);  
  command = Serial.read();    
  stoplightFunc(0, command);
  
  // get stoplight decision for intersection 1
  while(Serial.available() == 0);  
  command = Serial.read();  
  stoplightFunc(1, command);
}
