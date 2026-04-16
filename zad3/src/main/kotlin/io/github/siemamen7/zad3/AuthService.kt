package io.github.siemamen7.zad3

import org.springframework.stereotype.Component
import org.springframework.stereotype.Service

@Service
object AuthService {
    fun authenticate(username: String, password: String): Boolean {
        return username == "admin" && password == "1234"
    }

}