#!/bin/bash

BASE_URL="http://localhost:8000/products"

id1=1
id2=2

echo "=== TESTING POST ==="

response1=$(curl -s -X POST $BASE_URL \
  -H "Content-Type: application/json" \
  -d '{"name":"Test product 1","price":20.99}')

echo "Created product 1: $response1"

# Create product 2
response2=$(curl -s -X POST $BASE_URL \
  -H "Content-Type: application/json" \
  -d '{"name":"Test product 2","price":39.99}')

echo "Created product 2: $response2"

echo "=== TESTING GET ALL ==="
curl -s $BASE_URL
echo ""

echo ""
echo "=== TESTING GET BY ID ==="
curl -s $BASE_URL/$id1
echo ""

echo ""
echo "=== TESTING PUT ==="

curl -s -X PUT $BASE_URL/$id1 \
  -H "Content-Type: application/json" \
  -d '{"name":"Updated product 1","price":99.99}'

echo ""
echo "After update:"
curl -s $BASE_URL/$id1
echo ""

echo ""
echo "=== TESTING DELETE ==="

curl -s -X DELETE $BASE_URL/$id1
echo "Deleted product $id1"

curl -s -X DELETE $BASE_URL/$id2
echo "Deleted product $id2"

echo ""
echo "=== FINAL GET (should be empty) ==="
curl -s $BASE_URL
echo ""

echo ""
echo "=== TESTS COMPLETED ==="