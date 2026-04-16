package io.github.siemamen7.zad3

import org.springframework.beans.factory.ObjectProvider
import org.springframework.http.ResponseEntity
import org.springframework.web.bind.annotation.GetMapping
import org.springframework.web.bind.annotation.RequestBody
import org.springframework.web.bind.annotation.RestController

data class LoginRequest(val username: String, val password: String)
val items = listOf("Masło", "Polędwica", "Salceson", "Kapusta")

@RestController
class ItemsController(private val authServiceProvider: ObjectProvider<AuthService>) {

    @GetMapping("/items")
    fun getItems(@RequestBody login: LoginRequest): ResponseEntity<Any> {
        if (!authServiceProvider.getObject().authenticate(login.username, login.password)) {
            return ResponseEntity.status(401).body("Invalid credentials")
        }
        return ResponseEntity.ok(items)
    }
}