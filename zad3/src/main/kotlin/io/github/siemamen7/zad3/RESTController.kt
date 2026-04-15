package io.github.siemamen7.zad3

import org.springframework.web.bind.annotation.GetMapping
import org.springframework.web.bind.annotation.RequestMapping
import org.springframework.web.bind.annotation.RestController

@RestController
@RequestMapping("/api")
class RESTController {

    @GetMapping("/list")
    fun getItems() =
        listOf("Masło", "Polędwica", "Salceson", "Kapusta")
}