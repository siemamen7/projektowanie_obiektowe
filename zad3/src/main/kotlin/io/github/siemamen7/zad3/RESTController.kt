package io.github.siemamen7.zad3

import org.springframework.http.ResponseEntity
import org.springframework.web.bind.annotation.GetMapping
import org.springframework.web.bind.annotation.RequestBody
import org.springframework.web.bind.annotation.RequestMapping
import org.springframework.web.bind.annotation.RestController

data class LoginRequest(val username: String, val password: String)

@RestController
@RequestMapping("/api")
class RESTController {

    @GetMapping("/list")
    fun getItems(@RequestBody login: LoginRequest): ResponseEntity<List<String>> {

        listOf("Masło", "Polędwica", "Salceson", "Kapusta")
        }
}