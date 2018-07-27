int val1;
int val2;
void setup() {
  // put your setup code here, to run once:
  Serial.begin(9600);
  int i;
  
  for(i = 2; i <= 7; i++){
    pinMode(i, OUTPUT);

  pinMode(A2, INPUT);
  pinMode(A3, INPUT);
  }

  // 2 - 0 green light
  // 3 - 0 orange light
  // 4 - 0 red light
  // 5 - 1 green light
  // 6 - 1 orange light
  // 7 - 1 red light
}

void changeLight(){
  val1 = digitalRead(A2);
  val2 = digitalRead(A3);

  if(val1 == 0 && val2 == 0){
      digitalWrite(5, LOW);
      digitalWrite(6, HIGH);
      digitalWrite(7, LOW);
  }

  else if(val1 == 0 && val2 == 1){
      digitalWrite(2, HIGH);
      digitalWrite(3, LOW);
      digitalWrite(4, LOW);
      digitalWrite(5, LOW);
      digitalWrite(6, LOW);
      digitalWrite(7, HIGH);
  }

  else if(val1 == 1 && val2 == 0){
      digitalWrite(2, LOW);
      digitalWrite(3, HIGH);
      digitalWrite(4, LOW);
  }

  else if(val1 == 1 && val2 == 1){
      digitalWrite(2, LOW);
      digitalWrite(3, LOW);
      digitalWrite(4, HIGH);
      digitalWrite(5, HIGH);
      digitalWrite(6, LOW);
      digitalWrite(7, LOW);
  }
}

void loop() {
  // send data only when you receive data:
  changeLight();
}
