program zad1;

procedure generateRandom(var arr: array of Integer);
var
    i: Integer;
begin
    Randomize;
    for i := Low(arr) to High(arr) do
        arr[i] := Random(101);
end;

procedure bubbleSort(var arr: array of Integer);
var
    i, j, temp: Integer;
begin
    for i := Low(arr) to High(arr) - 1 do
        for j:= Low(arr) to High(arr) - 1 - i do
            if arr[j] > arr[j + 1] then
            begin
                temp := arr[j];
                arr[j] := arr[j + 1];
                arr[j + 1] := temp;
            end;
end;

var
    numbers: array[0..49] of Integer;
    i: Integer;

begin
    generateRandom(numbers);
    bubbleSort(numbers);

    for i := Low(numbers) to High(numbers) do
        WriteLn(numbers[i]);

end.
