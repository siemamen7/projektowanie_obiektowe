package io.github.siemamen7.zad3

import jakarta.annotation.PostConstruct
import org.springframework.context.annotation.Lazy
import org.springframework.context.annotation.Profile
import org.springframework.stereotype.Service

abstract class AuthService {
    fun authenticate(username: String, password: String): Boolean {
        return username == "admin" && password == "1234"
    }
}

@Service
@Profile("eager")
class AuthServiceEager : AuthService() {
    @PostConstruct
    fun init() {
        println("AuthServiceEager initialized")
    }
}

@Service
@Lazy
@Profile("lazy")
class AuthServiceLazy : AuthService() {
    @PostConstruct
    fun init() {
        println("AuthServiceLazy initialized")
    }
}