import { Component } from '@angular/core';
import { HeaderComponent } from "../../components/header/header.component";
import { FormControl, FormGroup, ReactiveFormsModule } from '@angular/forms';
import { uploadFormData, VideoService } from '../../../services/video/video.service';

@Component({
  selector: 'app-upload-video',
  standalone: true,
  imports: [HeaderComponent, ReactiveFormsModule],
  templateUrl: './upload-video.component.html',
  styleUrl: './upload-video.component.css'
})
export class UploadVideoComponent {

  public requiredFileType = 'video/mp4';
  public dragCounter = 0;

  video: File | null = null;
  public videoUrl: string | ArrayBuffer = '';

  uploadForm = new FormGroup({
    title: new FormControl(''),
    description: new FormControl(''),
  });

  // construct the component with the injected video service
  public constructor(private videoService: VideoService) { }

  // get the form group's title control
  public get title() {
    return this.uploadForm.get('title');
  }

  // get the form group's description control
  public get description() {
    return this.uploadForm.get('description');
  }

  // prevent default behavior upon being dragged over
  public onDragOver(event: Event): void {
    event.preventDefault();
  }

  // increment the counter upon drag start
  public onDragEnter(event: Event): void {
    event.preventDefault();

    this.dragCounter++;
  }

  // decrement the counter upon drag leave
  public onDragLeave(event: Event): void {
    this.dragCounter--;
  }

  // upon dropping, get the file that was dropped and call onFileChange with the
  // dropped file
  public onDropSuccess(event: any): void {
    event.preventDefault();
    this.dragCounter = 0;

    const files: FileList = event.dataTransfer.files
    if (!files || files.length === 0) {
      return;
    }

    this.onFileChange(event.dataTransfer.files);
  }

  // upon selecting a file, get the file and call onFileChange with the
  // selected file
  public onFileSelected(event: Event): void {
    const input: HTMLInputElement = event.currentTarget as HTMLInputElement;
    const files: FileList | null = input.files;
    if (!files || files.length === 0) {
      return;
    }

    this.onFileChange(files);
  }

  // Change the video to the given file. Read data from the file and update
  // videoUrl.
  private onFileChange(files: FileList): void {
    this.video = files.item(0)!;

    if (!this.requiredFileType.includes(this.video.type)) {
      return;
    }

    console.log(this.video);

    const reader = new FileReader();
    reader.onload = (event: ProgressEvent<FileReader>) => {
      this.videoUrl = event.target?.result || '';
    };
    reader.readAsDataURL(this.video);
  }

  // upon form submission, make a request to the back-end API with the video and
  // its details (title and description)
  public onSubmit(): void {
    const uploadData: uploadFormData = {
      video: this.video!,
      title: this.title?.value! ?? '',
      description: this.description?.value! ?? '',
    };

    this.videoService.uploadVideo(uploadData).subscribe({
      next: (value) => {
        console.log(value);
      },
      error: (error: any) => {
        console.log(error);
      }
    });
  }
}
