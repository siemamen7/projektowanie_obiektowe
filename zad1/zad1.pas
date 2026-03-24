program zad1;

type
    TIntArray = array of Integer;

procedure generateRandom(min, max, n: Integer; var arr: TIntArray);
var
    i: Integer;
begin
    SetLength(arr, n);
    Randomize;

    for i := Low(arr) to High(arr) do
        arr[i] := Random(max - min + 1) + min;
end;

procedure bubbleSort(var arr: TIntArray);
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
    numbers: TIntArray;
    i: Integer;

begin
    generateRandom(20, 50, 10, numbers);
    bubbleSort(numbers);

    for i := Low(numbers) to High(numbers) do
        WriteLn(numbers[i]);

end.
