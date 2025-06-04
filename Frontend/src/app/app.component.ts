import { Component, OnInit } from "@angular/core";
import { RouterOutlet } from "@angular/router";
import { AuthController } from "../controllers/auth.controller";

@Component({
  selector: "app-root",
  imports: [RouterOutlet],
  templateUrl: "./app.component.html",
  styleUrl: "./app.component.css",
})
export class AppComponent implements OnInit {
  constructor(private readonly authController: AuthController) {}

  async ngOnInit() {
    await this.authController.validate();
  }
}
